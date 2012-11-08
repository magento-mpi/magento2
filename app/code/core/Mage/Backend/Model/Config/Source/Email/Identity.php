<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Email_Identity implements Mage_Core_Model_Option_ArrayInterface
{
    protected $_options = null;
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $config = Mage::getSingleton('Mage_Backend_Model_Config_Structure_Reader')
                ->getConfiguration()
                ->getSection('trans_email');

            $groups = $config['groups'];
            foreach ($groups as $field) {
                $nodeName   = $field['id'];
                $label      = (string) isset($field['label']) ? $field['label'] : '';
                $sortOrder  = (int) isset($field['sortOrder']) ? $field['sortOrder'] : null;
                $this->_options[$sortOrder] = array(
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $nodeName),
                    'label' => Mage::helper('Mage_Backend_Helper_Data')->__($label)
                );
            }
            ksort($this->_options);
        }
        return $this->_options;
    }
}
