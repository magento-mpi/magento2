<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogProductEdit
 * Product edit page(backend)
 */
class CatalogProductEdit extends BackendPage
{
    const MCA = 'configurable/catalog/product/edit';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
