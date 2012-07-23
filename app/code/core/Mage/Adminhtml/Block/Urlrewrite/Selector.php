<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Modes selector for Urlrewrites modes
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Selector extends Mage_Core_Block_Template
{
    /**
     * List of available modes from source model
     * key => label
     *
     * @var array
     */
    protected $_modes;

    /**
     * Set block template and get available modes
     *
     */
    public function __construct( ) {
        $this->setTemplate('urlrewrite/selector.phtml');

        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->_modes = array(
            'category' => $helper->__('For category'),
            'product' => $helper->__('For product'),
            'cmspage' => $helper->__('For CMS page'),
            'id' => $helper->__('Custom'),
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
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create URL Rewrite:');
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
}
