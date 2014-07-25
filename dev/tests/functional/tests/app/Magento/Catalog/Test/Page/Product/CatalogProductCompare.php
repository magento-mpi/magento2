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
 * Frontend product compare page
 */
class CatalogProductCompare extends FrontendPage
{
    const MCA = 'catalog/product_compare/index';

    protected $_blocks = [
        'compareProductsBlock' => [
            'name' => 'compareProductsBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\Compare\ListCompare',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * Get compare products block
     *
     * @return \Magento\Catalog\Test\Block\Product\Compare\ListCompare
     */
    public function getCompareProductsBlock()
    {
        return $this->getBlockInstance('compareProductsBlock');
    }

    /**
     * Get message block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
