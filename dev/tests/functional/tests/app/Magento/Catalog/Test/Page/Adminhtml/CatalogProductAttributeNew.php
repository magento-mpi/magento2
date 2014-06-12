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
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Form',
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
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Form
     */
    public function getAttributeForm()
    {
        return $this->getBlockInstance('attributeForm');
    }
}
