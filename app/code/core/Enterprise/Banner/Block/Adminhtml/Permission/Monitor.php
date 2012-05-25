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
class Enterprise_Banner_Block_Adminhtml_Permission_Monitor extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing layout
     *
     * @return Enterprise_Banner_Block_Adminhtml_Permission_Monitor
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        if (!Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('cms/enterprise_banner')) {
            /** @var $layout Mage_Core_Model_Layout */
            $layout = $this->getLayout();
            if ($layout->getBlock('salesrule.related.banners') !== false) {
                /** @var $promoQouteEditTabsBlock Mage_Adminhtml_Block_Widget_Tabs */
                $promoQuoteEditTabsBlock = $layout->getBlock('promo_quote_edit_tabs');
                if ($promoQuoteEditTabsBlock !== false) {
                    $promoQuoteEditTabsBlock->removeTab('banners_section');
                    $layout->unsetElement('salesrule.related.banners');
                }
            } elseif ($layout->getBlock('catalogrule.related.banners') !== false) {
                /** @var $promoCatalogEditTabsBlock Mage_Adminhtml_Block_Widget_Tabs */
                $promoCatalogEditTabsBlock = $layout->getBlock('promo_catalog_edit_tabs');
                if ($promoCatalogEditTabsBlock !== false) {
                    $promoCatalogEditTabsBlock->removeTab('banners_section');
                    $layout->unsetElement('catalogrule.related.banners');
                }
            }
        }
        return $this;
    }
}
