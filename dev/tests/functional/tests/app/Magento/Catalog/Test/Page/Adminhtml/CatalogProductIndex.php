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
 * Class CatalogProductIndex
 */
class CatalogProductIndex extends BackendPage
{
    const MCA = 'catalog/product/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'productGrid' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Grid',
            'locator' => '#productGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActionBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\GridPageAction',
            'locator' => '#add_new_product',
            'strategy' => 'css selector',
        ],
        'accessDeniedBlock' => [
            'class' => 'Magento\Backend\Test\Block\Denied',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Grid
     */
    public function getProductGrid()
    {
        return $this->getBlockInstance('productGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\GridPageAction
     */
    public function getGridPageActionBlock()
    {
        return $this->getBlockInstance('gridPageActionBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Denied
     */
    public function getAccessDeniedBlock()
    {
        return $this->getBlockInstance('accessDeniedBlock');
    }
}
