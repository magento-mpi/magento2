<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price display type source model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_System_Config_Source_Tax_Display_Type
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX, 'label'=>__('Excluding Tax'));
            $this->_options[] = array('value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX, 'label'=>__('Including Tax'));
            $this->_options[] = array('value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH, 'label'=>__('Including and Excluding Tax'));
        }
        return $this->_options;
    }
}
