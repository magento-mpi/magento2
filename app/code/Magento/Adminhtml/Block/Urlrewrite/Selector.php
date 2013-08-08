<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Modes selector for URL rewrites modes
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Selector extends Magento_Core_Block_Template
{
    /**
     * List of available modes from source model
     * key => label
     *
     * @var array
     */
    protected $_modes;

    protected $_template = 'urlrewrite/selector.phtml';

    /**
     * Set block template and get available modes
     *
     */
    protected function _construct()
    {

        $this->_modes = array(
            'category' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('For category'),
            'product' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('For product'),
            'cms_page' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('For CMS page'),
            'id' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Custom'),
        );
    }

    /**
     * Available modes getter
     *
     * @return array
     */
    public function getModes()
    {
        return $this->_modes;
    }

    /**
     * Label getter
     *
     * @return string
     */
    public function getSelectorLabel()
    {
        return Mage::helper('Magento_Adminhtml_Helper_Data')->__('Create URL Rewrite:');
    }

    /**
     * Check whether selection is in specified mode
     *
     * @param string $mode
     * @return bool
     */
    public function isMode($mode)
    {
        return $this->getRequest()->has($mode);
    }

    /**
     * Get default mode
     *
     * @return string
     */
    public function getDefaultMode()
    {
        $keys = array_keys($this->_modes);
        return array_shift($keys);
    }
}
