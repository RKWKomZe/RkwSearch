<?php
namespace RKW\RkwSearch\Hooks;
use \RKW\RkwSearch\OrientDb\Helper\Common;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class ImportHookAbstract
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

abstract class ImportHookAbstract implements ImportHookInterface {

    /**
     * @var boolean Shows if ok message is already sent to prevent sending two of them
     */
    protected $okMessageSent = FALSE;

    /**
     * @var boolean Deactivates sending messages
     */
    protected $noMessages = FALSE;

    /**
     * @var boolean If set to TRUE the objects are marked with a special param
     */
    protected $debugMode = FALSE;

    /**
     * @var boolean If set to TRUE the imported data is shown in BE
     */
    protected $debugOutput = FALSE;

    /**
     * @var array Contains data to return if debug mode is active
     */
    protected $debugArray = NULL;

    /**
     * @var array Contains the configuration from TCA
     */
    protected $configuration;


    /**
     * Set the debug mode
     *
     * @param boolean $value
     * @return $this
     */
    public function setDebugMode($value) {
        $this->debugMode = (boolean) $value;
        return $this;
        //===
    }


    /**
     * Set the debug mode
     *
     * @param boolean $value
     * @return $this
     */
    protected function setDebugOutput($value) {
        $this->debugOutput = (boolean) $value;
        return $this;
        //===
    }


    /**
     * Set the debug array
     *
     * @param array $value
     * @return $this
     */
    public function setDebugArray($value) {
        $this->debugArray = $value;
        return $this;
        //===
    }


    /**
     * Gets the database object.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getTypo3Database() {
        return $GLOBALS['TYPO3_DB'];
        //===
    }



    /**
     * Debug function: Outputs error if any
     *
     * @param string $func Function calling debug()
     * @param array $data Data to debug
     * @return void
     */
    protected function debug($func, $data) {

        if ($this->debugOutput) {
            \TYPO3\CMS\Core\Utility\DebugUtility::debug(
                array(
                    'caller' => __NAMESPACE__ . '::' . $func,
                    'importData' => print_r($data, TRUE),
                    'debug_backtrace' => \TYPO3\CMS\Core\Utility\DebugUtility::debugTrail()
                ),
                $func,
                is_object($GLOBALS['error']) && @is_callable(array($GLOBALS['error'], 'debug'))
                    ? ''
                    : 'Import Debug'
            );

            //file_put_contents('/var/www/relaunch.rkw-kompetenzzentrum.de/public_html/data1.txt', print_r($data, TRUE));

        }
    }



    /**
     * Sends a message to the backend
     *
     * @param string $header The header of the message to be displayed in BE
     * @param string $message The message to be displayed in BE
     * @param string $code Sets the code for the BE-Message. Can be: NOTICE, INFO, OK (default), WARNING, ERROR
     * @param array $params Parameter for message
     * @return $this
     */
    protected function setBackendMessage($header, $message, $code = 'OK', $params = array()) {

        if ($this->noMessages)
            return $this;
            //===

        // set status
        $status = \TYPO3\CMS\Core\Messaging\FlashMessage::OK;
        switch (strtoupper($code)) {
            case 'NOTICE':
                $status = \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE;
                break;
            case 'INFO':
                $status = \TYPO3\CMS\Core\Messaging\FlashMessage::INFO;
                break;
            case 'WARNING':
                $status = \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING;
                break;
            case 'ERROR':
                $status = \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR;
        }

        // only send one OK- or NOTICE-Message
        if (
            (
                ($status == \TYPO3\CMS\Core\Messaging\FlashMessage::OK)
                || ($status == \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE)
            )
            && ($this->okMessageSent == TRUE)
        )
            return $this;
            //===

        if (
            ($status == \TYPO3\CMS\Core\Messaging\FlashMessage::OK)
            || ($status == \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE)
        )
            $this->okMessageSent = TRUE;


        // do translation and create message-object
        $message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($message, 'rkw_search', $params);
        $header = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($header, 'rkw_search');

        /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
        $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
            $message,
            $header, // the header is optional
            $status, // the severity is optional as well and defaults to \TYPO3\CMS\Core\Messaging\FlashMessage::OK
            TRUE // optional, whether the message should be stored in the session or only in the \TYPO3\CMS\Core\Messaging\FlashMessageQueue object (default is FALSE)
        );

        /** @var \TYPO3\CMS\Core\Messaging\FlashMessageService $flashMessageService */
        $flashMessageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');

        /** @var $flashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
        $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier('core.template.flashMessages');
        $flashMessageQueue->enqueue($flashMessage);

        return $this;
        //===
    }

    /**
     * Get configuration
     *
     * @return array
     */
    protected function getConfiguration() {

        // load from TypoScript
        if (! $this->configuration) {

            $settings = Common::getTyposcriptConfiguration();
            if ($settings['import'])
                $this->configuration = $settings['import'];
        }

        return $this->configuration;
        //===
    }


    /**
     * Constructor
     *
     */
    public function __construct() {

        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['debug']))
            $this->setDebugOutput($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['DB']['debug']);

    }

}
?>