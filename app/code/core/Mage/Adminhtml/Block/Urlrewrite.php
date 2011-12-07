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
 * Block for Urlrewrites grid container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Part for generating apropriate grid block name
     *
     * @var string
     */
    protected $_controller = 'urlrewrite';

    /**
     * Set custom labels and headers
     *
     */
    public function __construct()
    {
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('URL Rewrite Management');
        $this->_addButtonLabel = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite');
        parent::__construct();
    }

    /**
     * Customize grid row URLs
     *
     * @see Mage_Adminhtml_Block_Urlrewrite_Selector
     * @return string
     */
    public function getCreateUrl()
    {
        if (Mage::getBlockSingleton('Mage_Adminhtml_Block_Urlrewrite_Selector')) {
            $modes = array_keys(Mage::getBlockSingleton('Mage_Adminhtml_Block_Urlrewrite_Selector')->getModes());
            $url = $this->getUrl('*/*/edit') . array_shift($modes);
        } else {
            $url = $this->getUrl('*/*/edit');
        }
        return $url;
    }
}
