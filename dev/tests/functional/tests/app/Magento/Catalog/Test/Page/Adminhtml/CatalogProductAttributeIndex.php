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
 *
 * @package Magento\CustomAttributeManagement\Test\Page\Adminhtml
 */
class CatalogProductAttributeIndex extends BackendPage
{
    const MCA = 'catalog/product_attribute/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'blockPageActionsAttribute' => [
            'name' => 'blockPageActionsAttribute',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'name' => 'grid',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Grid',
            'locator' => '#attributeGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\FormPageActions
     */
    public function getBlockPageActionsAttribute()
    {
        return $this->getBlockInstance('blockPageActionsAttribute');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Grid
     */
    public function getBlockAttributeGrid()
    {
        return $this->getBlockInstance('grid');
    }
}
