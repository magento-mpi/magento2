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
 * Block for Urlrewrites grid container
 *
 * @method Magento_Adminhtml_Block_Urlrewrite setSelectorBlock(Magento_Adminhtml_Block_Urlrewrite_Selector $value)
 * @method null|Magento_Adminhtml_Block_Urlrewrite_Selector getSelectorBlock()
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite extends Magento_Adminhtml_Block_Widget_Grid_Container
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
    protected function _construct()
    {
        $this->_headerText = __('URL Rewrite Management');
        $this->_addButtonLabel = __('Add URL Rewrite');
        parent::_construct();
    }

    /**
     * Customize grid row URLs
     *
     * @see Magento_Adminhtml_Block_Urlrewrite_Selector
     * @return string
     */
    public function getCreateUrl()
    {
        $url = $this->getUrl('*/*/edit');

        $selectorBlock = $this->getSelectorBlock();
        if ($selectorBlock === null) {
            $selectorBlock = Mage::getBlockSingleton('Magento_Adminhtml_Block_Urlrewrite_Selector');
        }

        if ($selectorBlock) {
            $modes = array_keys($selectorBlock->getModes());
            $url .= reset($modes);
        }

        return $url;
    }
}
