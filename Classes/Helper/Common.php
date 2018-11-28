<?php

namespace RKW\RkwSearch\Helper;

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
 * Class Common
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Common extends \RKW\RkwBasics\Helper\Common {


    /**
     * @var array Setter/Getter classShortName transformation cache
     */
    protected static $_classShortNameCache = array();

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected static $_logger;



    /**
     * strips class name from path
     *
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string|object $class
     * @param boolean $getModel
     * @return string
     */
    public static function getShortName($class, $getModel = FALSE) {

        if (is_object($class))
            $class = get_class($class);

        if (isset(self::$_classShortNameCache[sha1((string) $class . (string) $getModel)])) {
            return self::$_classShortNameCache[sha1((string) $class . (string) $getModel)];
            //===
        }

        $result = substr($class, intval(strrpos($class, '\\'))+1);
        if (
            ($getModel)
            && (strrpos($result, 'Repository') !== FALSE)
        )
            $result = substr($result, 0, intval(strrpos($result, 'Repository')));

        self::$_classShortNameCache[sha1((string) $class . (string) $getModel)] = $result;
        return $result;
        //===
    }


    /**
     * Get TypoScript configuration
     *
	 * @param string $extension
	 * @param string $type
     * @return array
     */
    public static function getTyposcriptConfiguration($extension = NULL, $type = \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS) {

        return parent::getTyposcriptConfiguration('Rkwsearch');
        //===

    }

}