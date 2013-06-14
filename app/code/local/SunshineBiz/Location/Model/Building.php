<?php

/**
 * SunshineBiz_Location building model
 *
 * @category   SunshineBiz
 * @package    SunshineBiz_Location
 * @author     iSunshineTech <isunshinetech@gmail.com>
 * @copyright   Copyright (c) 2013 SunshineBiz.commerce, Inc. (http://www.sunshinebiz.cn)
 */
class SunshineBiz_Location_Model_Building extends SunshineBiz_Locale_Model_Abstract {

    protected $_helper;

    protected function _construct() {
        $this->_init('location/building');
        $this->_helper = Mage::helper('location');
    }
    
    protected function _beforeSave() {
        
        if (!Zend_Validate::is($this->getDefaultName(), 'NotEmpty'))
            Mage::throwException($this->_helper->__('Default name is required field.'));
        
        if (!Zend_Validate::is($this->getAreaId(), 'NotEmpty'))
            Mage::throwException($this->_helper->__('Area is required field.'));
        
        $area = Mage::getModel('location/area')->load($this->getAreaId());
        if($area->getType() != SunshineBiz_Location_Model_Area::TYPE_ADMINISTRATIVE_REGION_LEVEL_3)
            Mage::throwException($this->_helper->__('Area must be Level 3 Administrative Region.'));
        
        if (!Zend_Validate::is($this->getDefaultAddress(), 'NotEmpty'))
            Mage::throwException($this->_helper->__('Default address is required field.'));
        
        return parent::_beforeSave();
    }
    
    protected function _getValidationRulesBeforeSave() {
        
        $defaultNameNotEmpty = new Zend_Validate_NotEmpty();
        $defaultNameNotEmpty->setMessage(
            $this->_helper->__('Default name is required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        
        $areaNotEmpty = new Zend_Validate_NotEmpty();
        $areaNotEmpty->setMessage(
            $this->_helper->__('Area is required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        
        $defaultAddressNotEmpty = new Zend_Validate_NotEmpty();
        $defaultAddressNotEmpty->setMessage(
            $this->_helper->__('Default address is required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        
        $validator = Mage::getModel('Magento_Validator_Composite_VarienObject');
        $validator->addRule($defaultNameNotEmpty, 'default_name')
                ->addRule($areaNotEmpty, 'area_id')
                ->addRule($defaultAddressNotEmpty, 'default_address');
        
        return $validator;
    }

    public function getLocaleMnemonic() {
        return $this->getData('mnemonic');
    }

    public function getMnemonic() {
        
        $name = $this->getLocaleMnemonic();
        
        if (is_null($name)) {
            $name = $this->getData('default_mnemonic');
        }
        return $name;
    }

    public function getLocaleAddress() {
        return $this->getData('address');
    }

    public function getAddress() {
        
        $name = $this->getLocaleAddress();
        
        if (is_null($name)) {
            $name = $this->getData('default_address');
        }
        return $name;
    }
}