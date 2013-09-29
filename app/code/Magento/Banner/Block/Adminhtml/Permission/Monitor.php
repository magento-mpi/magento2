<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Banner Permission Monitor block
 *
 * Removes certain blocks from layout if user do not have required permissions
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Permission;

class Monitor extends \Magento\Adminhtml\Block\Template
{
    /**
     * Preparing layout
     *
     * @return \Magento\Banner\Block\Adminhtml\Permission\Monitor
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (!$this->_authorization->isAllowed('Magento_Banner::magento_banner')) {
            /** @var $layout \Magento\Core\Model\Layout */
            $layout = $this->getLayout();
            if ($layout->getBlock('salesrule.related.banners') !== false) {
                /** @var $promoQuoteBlock \Magento\Adminhtml\Block\Widget\Tabs */
                $promoQuoteBlock = $layout->getBlock('promo_quote_edit_tabs');
                if ($promoQuoteBlock !== false) {
                    $promoQuoteBlock->removeTab('banners_section');
                    $layout->unsetElement('salesrule.related.banners');
                }
            } elseif ($layout->getBlock('catalogrule.related.banners') !== false) {
                /** @var $promoCatalogBlock \Magento\Adminhtml\Block\Widget\Tabs */
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
