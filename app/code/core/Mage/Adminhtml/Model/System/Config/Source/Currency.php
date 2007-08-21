<?php

class Mage_Adminhtml_Model_System_Config_Source_Currency
{
    protected $_options;
    
    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('directory/currency_collection')
                ->addLanguageFilter('en')
                ->loadData()
                ->toOptionArray();
        }
        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }
}