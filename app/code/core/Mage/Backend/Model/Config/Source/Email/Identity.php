<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Email_Identity
{
    protected $_options = null;
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $config = Mage::getSingleton('Mage_Backend_Model_Config_Structure')
                ->getSection('trans_email');

            $fields = $config['groups']['fields'];
            foreach ($fields as $field) {
                $nodeName   = $field['id'];
                $label      = (string) isset($field['label']) ? $field['label'] : '';
                $sortOrder  = (int) isset($field['sortOrder'] ? $field['sort_order'] : null;
                $this->_options[$sortOrder] = array(
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $nodeName),
                    'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__($label)
                );
            }
            ksort($this->_options);
        }

        return $this->_options;
    }
}
