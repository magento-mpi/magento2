<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogProductCompare
 */
class CatalogProductCompare extends FrontendPage
{
    const MCA = 'catalog/product_compare/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'compareProductsBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\Compare\ListCompare',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Product\Compare\ListCompare
     */
    public function getCompareProductsBlock()
    {
        return $this->getBlockInstance('compareProductsBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
