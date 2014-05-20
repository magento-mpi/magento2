<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomAttributeManagement\Test\Page\Adminhtml;

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
        'blockPageActionsAttribute' => [
            'name' => 'blockPageActionsAttribute',
            'class' => 'Magento\CustomAttributeManagement\Test\Block\Adminhtml\Product\Attribute\FormPageActions',
            'locator' => '#page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomAttributeManagement\Test\Block\Adminhtml\Product\Attribute\FormPageActions
     */
    public function getBlockPageActionsAttribute()
    {
        return $this->getBlockInstance('blockPageActionsAttribute');
    }
}
