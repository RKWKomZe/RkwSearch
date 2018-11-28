<?php
namespace RKW\RkwSearch\OrientDb\Domain\Model;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\OrientDb\Helper\Tca\OrientDbFields;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;
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
 * Class ModelAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

abstract class ModelAbstract implements ModelInterface{

    /**
     * Record type Document
     * @var string
     */
    const RECORD_TYPE_DOCUMENT = 'd';

    /**
     * @var int The timestamp of the last change
     */
    protected $tstamp;

    /**
     * @var int Create date
     */
    protected $crdate;

    /**
     * @var boolean debug mode
     */
    protected $debug;

    /**
     * @var int The language uid
     */
    protected $sysLanguageUid;


    /**
     * @var string The uid/rid of the object
     */
    protected $_rid;

    /**
     * @var string OrientDb class name
     */
    protected $_class;

    /**
     * @var string OrientDb type
     */
    protected $_type;

    /**
     * @var string OrientDb type
     */
    protected $_version;


    /**
     * @var array Contains all properties that have been changed
     */
    protected $_dataChanged = array();


    /**
     * Returns a hash map of property names and property values.
     *
     * @param array|object $data
     * @param boolean $inverse
     * @return $this
     * @api
     */
    public function setProperties($data, $inverse = FALSE) {

        // handling for arrays
        if (
            (! empty($data))
            && (is_array($data))
        ) {

            foreach ($data as $property => $value) {
                $this->setProperty($property, $value);
            }

        // handling for objects
        } else if (is_object($data)) {

            $methods = get_class_methods($data);
            foreach ($methods as $method) {
                if (strpos($method, 'get') === 0) {
                    $property = substr($method, 3);
                    $this->setProperty($property, $data->$method());
                }
            }


        }

        return $this;
        //===
    }


    /**
     * Returns a hash map of property names and property values.
     *
     * @return array The properties
     * @api
     */
    public function getProperties() {
        $properties = get_object_vars($this);
        foreach ($properties as $propertyName => $propertyValue) {
            if ($propertyName[0] === '_') {
                unset($properties[$propertyName]);
            }
        }

        return $properties;
        //===
    }


    /**
     * Returns a hash map of all changed property names and property values since model has been loaded
     *
     * @param string $filter If set some properties are filtered out. Needed for checksum-validation and update!
     * @return array The properties
     * @api
     */
    public function getPropertiesChanged($filter = NULL) {

        $returnArray = $this->_dataChanged;
        if ($filter) {

            unset($returnArray['uid']);
            unset($returnArray['sysLanguageUid']);

            if ($filter == 'checksum') {
                unset($returnArray['tstamp']);
            }

        }

        return $returnArray;
        //===
    }


    /**
     * Reconstitutes a property.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @return $this
     */
    protected function setProperty($propertyName, $propertyValue) {

        // check for mapping
        $propertyName = Common::camelize($propertyName);

        // treatment for some special fields
        // this way internally we can use a fixed name while the property name in the model is configurable via TCA
        if ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if ($newPropertyName = OrientDbFields::getCtrlEnableField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageUid')
            && ($newPropertyName = OrientDbFields::getLanguageField(Common::getShortName($this)))
        )
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageOverlayUid')
            && ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), 'transOrigPointerField'))
        )
            $propertyName = $newPropertyName;

        // set property
        if ($this->hasProperty($propertyName)) {
            $this->$propertyName = $propertyValue;
            $this->_dataChanged[$propertyName] = $propertyValue;

        }


        // set internal properties
        if ($propertyName == '@class') {
            $this->_class = $propertyValue;

        } else if ($propertyName == '@type') {
            $this->_type = $propertyValue;

        } else if ($propertyName == '@version') {
            $this->_version = $propertyValue;

        } else if (
            ($propertyName == '@rid')
            || ($propertyName == 'rid')
        ){
            $this->_rid = $propertyValue;
        }

        return $this;
        //===
    }


    /**
     * Returns the property value of the given property name.
     *
     * @param string $propertyName
     * @return mixed The propertyValue
     */
    protected function getProperty($propertyName) {

        $propertyName = Common::camelize($propertyName);

        // treatment for some special fields
        // this way internally we can use a fixed name while the property name in the model is configurable via TCA
        if ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if ($newPropertyName = OrientDbFields::getCtrlEnableField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageUid')
            && ($newPropertyName = OrientDbFields::getLanguageField(Common::getShortName($this)))
        )
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageOverlayUid')
            && ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), 'transOrigPointerField'))
        )
            $propertyName = $newPropertyName;


        if ($this->hasProperty($propertyName))
            return $this->$propertyName;
            //===


        // get internal properties
        if ($propertyName == 'class') {

            if ($this->_class)
                return $this->_class;
                //===

            return Common::getShortName($this);
            //===

        } else if ($propertyName == 'type') {

            if ($this->_type)
                return $this->_type;
                //===

            return self::RECORD_TYPE_DOCUMENT;
            //===

        } else if ($propertyName == 'version') {
            return $this->_version;
            //===

        } else if ($propertyName == 'rid'){
            return $this->_rid;
            //===
        }

        return NULL;
        //===
    }


    /**
     * Returns the property value of the given property name.
     *
     * @param string $propertyName
     * @return boolean
     */
    protected function unsetProperty($propertyName) {

        $propertyName = Common::camelize($propertyName);

        // treatment for some special fields
        // this way internally we can use a fixed name while the property name in the model is configurable via TCA
        if ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if ($newPropertyName = OrientDbFields::getCtrlEnableField(Common::getShortName($this), $propertyName))
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageUid')
            && ($newPropertyName = OrientDbFields::getLanguageField(Common::getShortName($this)))
        )
            $propertyName = $newPropertyName;

        if (
            ($propertyName == 'languageOverlayUid')
            && ($newPropertyName = OrientDbFields::getCtrlField(Common::getShortName($this), 'transOrigPointerField'))
        )
            $propertyName = $newPropertyName;


        if ($this->hasProperty($propertyName)) {
            $this->$propertyName = NULL;
            unset($this->_dataChanged[$propertyName]);

            return TRUE;
            //===
        }

        return FALSE;
        //===
    }


    /**
     * Returns the property value of the given property name. Only for internal use.
     *
     * @param string $propertyName
     * @return boolean
     */
    protected function hasProperty($propertyName) {
        return property_exists($this, $propertyName) && ($propertyName[0] !== '_');
        //===
    }


    /**
     * Get cluster-id from rid
     *
     * @return integer
     */
    public function getClusterId() {

        return intval(substr($this->getProperty('rid'), intval(strpos($this->getProperty('rid'), '#'))+1, intval(strpos($this->getProperty('rid'), ':'))-1));
        //===
    }

    /**
     * Get position of record from rid
     *
     * @return integer
     */
    public function getPositionId() {

        return intval(substr($this->getProperty('rid'), intval(strpos($this->getProperty('rid'), ':'))+1));
        //===
    }

    /**
     * Get rid
     *
     * @return string
     */
    public function getRid() {
        return $this->getProperty('rid');
        //===
    }


    /**
     * Set rid
     *
     * @param string $value
     * @return void
     */
    public function setRid($value) {
        $this->setProperty('rid', $value);
    }


    /**
     * Get class
     *
     * @return string
     */
    public function getClass() {
        return $this->getProperty('class');
        //===
    }


    /**
     * Set class
     *
     * @param string $value
     * @return void
     */
    public function setClass($value){
        $this->setProperty('class', $value);
    }


    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->getProperty('type');
        //===
    }


    /**
     * Set type
     *
     * @param string $value
     * @return void
     */
    public function setType($value){
        $this->setProperty('type', $value);
    }


    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion(){
        return $this->getProperty('version');
        //===
    }


    /**
     * Set version
     *
     * @param integer $value
     * @return void
     */
    public function setVersion($value) {
        $this->setProperty('version', $value);
    }





    /*********************************************
     * Magic methods
     *********************************************/

    /**
     * Calls internal getter- and setter-method
     *
     * @param string $name name of method
     * @param array $arguments arguments for method
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function __call($name, $arguments) {
        $property = lcfirst(substr_replace($name, '', 0, 3));
        if (strpos($name, 'get', 0) !== FALSE) {
            return $this->getProperty($property);
            //===

        } elseif (strpos($name, 'set', 0) !== FALSE) {
            return $this->setProperty($property, $arguments[0], $arguments[1]);
            //===

        }  elseif (strpos($name, 'has', 0) !== FALSE) {
            return $this->hasProperty($property);
            //===

        } elseif (strpos($name, 'uns', 0) !== FALSE) {
            return $this->unsetProperty($property);
            //===
        }

        throw new \RKW\RkwSearch\Exception('Method "' . $name . '" is not defined.', 1396947225);
        //===
    }

    /**
     * constructor
     *
     * @param array|object $data
     * @api
     */
    public function __construct($data = array()) {

        $this->setProperties($data);

    }
}
