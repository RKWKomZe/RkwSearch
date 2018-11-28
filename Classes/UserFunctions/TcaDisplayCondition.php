<?php

namespace RKW\RkwSearch\UserFunctions;

use \RKW\RkwSearch\Helper\Common;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
 * Class TcaDisplayCondition
 *
 * @package   RKW_RkwSearch
 * @author    Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */
class TcaDisplayCondition
{

    /**
     * @var array Contains the TypoScript settings
     */
    protected $settings;


    /**
     * Checks if given page is in rootline and displays/hides elements accordingly
     *
     * @param array $configuration
     * @return bool
     */
    public function displayIfInRootLine(array $configuration)
    {

        if (
            ($pid = $configuration['record']['uid'])
            && ($rootPages = GeneralUtility::trimExplode(',', $this->getConfiguration('rootPages')))
            && (!empty($rootPages))
        ) {

            // get root line of page
            $rootLine = BackendUtility::BEgetRootLine($pid);

            // check
            if (is_array($rootLine)) {
                foreach ($rootLine as $level => $data) {
                    if (in_array($data['uid'], $rootPages)) {
                        return true;
                        //===
                    }
                }

                return false;
                //===
            }
        }

        return true;
        //===
    }


    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed
     */
    protected function getConfiguration($key = null)
    {

        if (!$this->settings) {

            // load from TypoScript
            $settings = Common::getTyposcriptConfiguration();
            if ($settings['import']) {
                $this->settings = $settings['import'];
            }
        }

        if ($key) {

            if ($this->settings[$key]) {
                return $this->settings[$key];
                //===
            }

            return $this->settings[$key];
            //===
        }

        return $this->settings;
        //===
    }
}