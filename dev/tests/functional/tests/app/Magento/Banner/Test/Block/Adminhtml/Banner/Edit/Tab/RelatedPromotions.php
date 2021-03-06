<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Test\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Banner\Test\Block\Adminhtml\Promo\CartPriceRulesGrid;
use Magento\Banner\Test\Block\Adminhtml\Promo\CatalogPriceRulesGrid;
use Mtf\Client\Element;

/**
 * Class RelatedPromotions
 * Banner related promotions per store view edit page
 */
class RelatedPromotions extends Tab
{
    /**
     * Locator for Sales Rule Grid
     *
     * @var string
     */
    protected $salesRuleGrid = '#related_salesrule_grid';

    /**
     * Locator for Catalog Rule Grid
     *
     * @var string
     */
    protected $catalogRuleGrid = '#related_catalogrule_grid';

    /**
     * Get Cart Price Rules grid on the Banner New page
     *
     * @return CartPriceRulesGrid
     */
    public function getCartPriceRulesGrid()
    {
        return $this->blockFactory->create(
            'Magento\Banner\Test\Block\Adminhtml\Promo\CartPriceRulesGrid',
            [
                'element' => $this->_rootElement->find($this->salesRuleGrid)
            ]
        );
    }

    /**
     * Get Catalog Price Rules grid on the Banner New page
     *
     * @return CatalogPriceRulesGrid
     */
    public function getCatalogPriceRulesGrid()
    {
        return $this->blockFactory->create(
            'Magento\Banner\Test\Block\Adminhtml\Promo\CatalogPriceRulesGrid',
            [
                'element' => $this->_rootElement->find($this->catalogRuleGrid)
            ]
        );
    }
}
