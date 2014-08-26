<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogRuleIndex
 */
class CatalogRuleIndex extends BackendPage
{
    const MCA = 'catalog_rule/promo_catalog/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'class' => 'Magento\CatalogRule\Test\Block\Adminhtml\Promo\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'catalogRuleGrid' => [
            'class' => 'Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog',
            'locator' => '#promo_catalog_grid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\CatalogRule\Test\Block\Adminhtml\Promo\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog
     */
    public function getCatalogRuleGrid()
    {
        return $this->getBlockInstance('catalogRuleGrid');
    }
}
