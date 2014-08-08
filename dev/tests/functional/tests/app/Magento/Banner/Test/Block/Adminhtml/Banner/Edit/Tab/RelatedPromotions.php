<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Test\Block\Adminhtml\Banner\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class RelatedPromotions
 * Banner related promotions per store view edit page
 */
class RelatedPromotions extends Tab
{
    /**
     * Get Cart Price Rules grid on the Banner New page
     *
     * @return \Mtf\Block\BlockInterface
     */
    public function getCartPriceRulesGrid()
    {
        return $this->blockFactory->create(
            'Magento\Banner\Test\Block\Adminhtml\Promo\CartPriceRulesGrid',
            [
                'element' => $this->_rootElement->find('#related_salesrule_grid')
            ]
        );
    }

    /**
     * Get Catalog Price Rules grid on the Banner New page
     *
     * @return \Mtf\Block\BlockInterface
     */
    public function getCatalogPriceRulesGrid()
    {
        return $this->blockFactory->create(
            'Magento\Banner\Test\Block\Adminhtml\Promo\CatalogPriceRulesGrid',
            [
                'element' => $this->_rootElement->find('#related_catalogrule_grid')
            ]
        );
    }
}
