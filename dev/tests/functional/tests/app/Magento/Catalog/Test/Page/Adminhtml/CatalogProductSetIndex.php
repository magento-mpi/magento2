<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogProductSetIndex
 * Product Set page
 */
class CatalogProductSetIndex extends BackendPage
{
    const MCA = 'catalog/product_set/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'attributeSetGrid' => [
            'name' => 'attributeSetGrid',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Grid',
            'locator' => '#setGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set\Grid
     */
    public function getAttributeSetGrid()
    {
        return $this->getBlockInstance('attributeSetGrid');
    }
}
