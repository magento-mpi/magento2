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
 * Class CatalogProductAttributeNew
 * Product Attribute New page
 */
class CatalogProductAttributeNew extends BackendPage
{
    const MCA = 'catalog/product_attribute/new';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'attributeForm' => [
            'name' => 'attributeForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\AttributeForm',
            'locator' => '[id$="main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\AttributeForm
     */
    public function getAttributeForm()
    {
        return $this->getBlockInstance('attributeForm');
    }
}
