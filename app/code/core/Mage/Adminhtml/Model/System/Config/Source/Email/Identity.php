<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Email_Identity
{
    protected $_options = null;
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $config = Mage::getSingleton('Mage_Adminhtml_Model_Config')->getSection('trans_email')->groups->children();
            foreach ($config as $node) {
                $nodeName   = $node->getName();
                $label      = (string) $node->label;
                $sortOrder  = (int) $node->sort_order;
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
