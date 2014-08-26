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
 * Class CatalogProductAttributeIndex
 */
class CatalogProductAttributeIndex extends BackendPage
{
    const MCA = 'catalog/product_attribute/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '#page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Grid',
            'locator' => '.grid',
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
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }
}
