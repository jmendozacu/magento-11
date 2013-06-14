<?php

/**
 * SunshineBiz_Location area edit form block
 *
 * @category   SunshineBiz
 * @package    SunshineBiz_Location
 * @author     iSunshineTech <isunshinetech@gmail.com>
 * @copyright   Copyright (c) 2013 SunshineBiz.commerce, Inc. (http://www.sunshinebiz.cn)
 */
class SunshineBiz_Location_Block_Area_Edit_Form extends SunshineBiz_Location_Block_Widget_Form {

    public function __construct() {

        parent::__construct();
        $this->setId('area_form');

        $this->setTitle($this->_helper->__('Area Information'));
    }

    protected function _prepareForm() {

        $model = Mage::registry('locations_area');
        $form = new SunshineBiz_Locale_Block_Data_Form(
                array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );
        $form->setHtmlIdPrefix('area_');

        $fieldset = $form->addFieldset(
                'base_fieldset', array('legend' => $this->_helper->__('Area Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));

            $_locales = array();
            if($model->getNames()) {
                foreach ($model->getNames() as $name) {
                    if($name['locale'])
                        $_locales[] = $name['locale'];
                }            
            }
            $str = str_ireplace('"', '\'', json_encode($_locales));
            $locales = Mage::app()->getLocale()->getTranslatedOptionLocales();
            array_unshift($locales, array('value' => '', 'label' => $this->_helper->__('Default Locale')));
            $fieldset->addField('locale', 'select', array(
                'name' => 'locale',
                'label' => Mage::helper('locale')->__('Locale'),
                'onchange' => "localeChanged(this, '{$this->_getChangeUrl()}', {$str});",
                'id' => 'locale',
                'title' => Mage::helper('locale')->__('Locale'),
                'class' => 'input-select'
            ))->setValues($locales);
        } else {
            if (!$model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $fieldset->addField('default_name', 'text', array(
            'name' => 'default_name',
            'label' => $this->_helper->__('Default Name'),
            'id' => 'default_name',
            'title' => $this->_helper->__('Default Name'),
            'required' => true,
        ));
        
        if($model->getNames()) {
            $i = 0;
            foreach ($model->getNames() as $name) {
                if($name['locale']) {
                    $fieldset->addField("names[{$i}][locale]", 'hidden', array(
                        'name' => "names[{$i}][locale]",
                    ));

                    $label = $this->_helper->getLocaleLabel($name['locale']);
                    $fieldset->addField("names[{$i}][name]", 'text', array(
                        'name' => "names[{$i}][name]",
                        'label' => $this->_helper->__('%s Name', $label),
                        'id' => "names[{$i}][name]",
                        'title' => $this->_helper->__('%s Name', $label),
                    ));
                    $i++;
                }
            }
        }
        
        $fieldset->addField('default_abbr', 'text', array(
            'name' => 'default_abbr',
            'label' => $this->_helper->__('Default Abbr'),
            'id' => 'default_abbr',
            'title' => $this->_helper->__('Default Abbr'),
        ));
        
        if($model->getNames()) {
            $i = 0;
            foreach ($model->getNames() as $name) {
                if($name['locale']) {
                    $label = $this->_helper->getLocaleLabel($name['locale']);
                    $fieldset->addField("names[{$i}][abbr]", 'text', array(
                        'name' => "names[{$i}][abbr]",
                        'label' => $this->_helper->__('%s Abbr', $label),
                        'id' => "names[{$i}][abbr]",
                        'title' => $this->_helper->__('%s Abbr', $label),
                    ));
                    $i++;
                }
            }
        }
        
        $fieldset->addField('default_mnemonic', 'text', array(
            'name' => 'default_mnemonic',
            'label' => $this->_helper->__('Default Mnemonic'),
            'id' => 'default_mnemonic',
            'title' => $this->_helper->__('Default Mnemonic'),
        ));
        
        if($model->getNames()) {
            $i = 0;
            foreach ($model->getNames() as $name) {
                if($name['locale']) {
                    $label = $this->_helper->getLocaleLabel($name['locale']);
                    $fieldset->addField("names[{$i}][mnemonic]", 'text', array(
                        'name' => "names[{$i}][mnemonic]",
                        'label' => $this->_helper->__('%s Mnemonic', $label),
                        'id' => "names[{$i}][mnemonic]",
                        'title' => $this->_helper->__('%s Mnemonic', $label),
                    ));
                    $i++;
                }
            }
        }

        $fieldset->addField('country_id', 'select', array(
            'name' => 'country_id',
            'label' => $this->_helper->__('Country'),
            'id' => 'country_id',
            'title' => $this->_helper->__('Country'),
            'class' => 'input-select',
            'onchange' => 'locationChanged(this, \'' . $this->getUrl('*/json/countryRegion') . '\',  \'area_region_id\')'
        ))->setValues(Mage::getResourceModel('directory/country_collection')->load()->toOptionArray());

        $fieldset->addField('region_id', 'select', array(
            'name' => 'region_id',
            'label' => $this->_helper->__('Region'),
            'id' => 'region_id',
            'title' => $this->_helper->__('Region'),
            'class' => 'input-select',
            'onchange' => 'locationChanged(this, \'' . $this->getUrl('*/json/regionArea') . '\',  \'area_parent_id\')',
            'required' => true,
        ))->setValues(Mage::getModel('directory/country')->setId($model->getCountryId())->getRegions()->toOptionArray());

        $options = array();
        if ($model->getRegionId()) {
            $options = Mage::getResourceModel('location/area_collection')
                ->addRegionFilter($model->getRegionId())
                ->addFieldToFilter('type', array('lteq' => SunshineBiz_Location_Model_Area::TYPE_ADMINISTRATIVE_REGION_LEVEL_3))
                ->load()
                ->toOptionArray(false);
        }
        array_unshift($options, array('value' => '0', 'label' => Mage::helper('core')->__('-- Please Select --')));
        $fieldset->addField('parent_id', 'select', array(
            'name' => 'parent_id',
            'label' => $this->_helper->__('Parent'),
            'id' => 'parent_id',
            'title' => $this->_helper->__('Parent'),
            'class' => 'input-select'
        ))->setValues($options);
        
        $fieldset->addField('type', 'select', array(
            'name' => 'type',
            'label' => $this->_helper->__('Type'),
            'id' => 'type',
            'title' => $this->_helper->__('Type'),
            'class' => 'input-select',
            'options' => array(
                SunshineBiz_Location_Model_Area::TYPE_ADMINISTRATIVE_REGION_LEVEL_2 => $this->_helper->__('Level 2 Administrative Region'),
                SunshineBiz_Location_Model_Area::TYPE_ADMINISTRATIVE_REGION_LEVEL_3 => $this->_helper->__('Level 3 Administrative Region'),
                SunshineBiz_Location_Model_Area::TYPE_TRADE_CIRCLE => $this->_helper->__('Trade Circle')
            ),
            'required' => true,
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'name' => 'is_active',
            'label' => $this->_helper->__('Status'),
            'id' => 'is_active',
            'title' => $this->_helper->__('Status'),
            'class' => 'input-select',
            'options' => array(
                '1' => $this->_helper->__('Active'),
                '0' => $this->_helper->__('Inactive')
            ),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getChangeUrl() {

        $model = Mage::registry('locations_area');

        return $this->getUrl('*/*/edit', array(
                    'id' => $model->getId()
                ));
    }
}