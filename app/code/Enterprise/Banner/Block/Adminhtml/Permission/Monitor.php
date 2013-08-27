<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner Permission Monitor block
 *
 * Removes certain blocks from layout if user do not have required permissions
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Permission_Monitor extends Magento_Adminhtml_Block_Template
{
    /**
     * Preparing layout
     *
     * @return Enterprise_Banner_Block_Adminhtml_Permission_Monitor
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (!$this->_authorization->isAllowed('Enterprise_Banner::enterprise_banner')) {
            /** @var $layout Magento_Core_Model_Layout */
            $layout = $this->getLayout();
            if ($layout->getBlock('salesrule.related.banners') !== false) {
                /** @var $promoQuoteBlock Magento_Adminhtml_Block_Widget_Tabs */
                $promoQuoteBlock = $layout->getBlock('promo_quote_edit_tabs');
                if ($promoQuoteBlock !== false) {
                    $promoQuoteBlock->removeTab('banners_section');
                    $layout->unsetElement('salesrule.related.banners');
                }
            } elseif ($layout->getBlock('catalogrule.related.banners') !== false) {
                /** @var $promoCatalogBlock Magento_Adminhtml_Block_Widget_Tabs */
                $promoCatalogBlock = $layout->getBlock('promo_catalog_edit_tabs');
                if ($promoCatalogBlock !== false) {
                    $promoCatalogBlock->removeTab('banners_section');
                    $layout->unsetElement('catalogrule.related.banners');
                }
            }
        }
        return $this;
    }
}
