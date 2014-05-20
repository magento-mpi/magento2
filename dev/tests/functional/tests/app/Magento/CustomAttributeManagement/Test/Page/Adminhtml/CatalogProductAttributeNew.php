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
 * Class CatalogProductAttributeNew
 *
 * @package Magento\CustomAttributeManagement\Test\Page\Adminhtml
 */
class CatalogProductAttributeNew extends BackendPage
{
    const MCA = 'catalog/product_attribute/new';

    protected $_blocks = [
        'testBlock' => [
            'name' => 'testBlock',
            'class' => 'Magento\Mtf\Test\Block\TestBlock',
            'locator' => 'body',
            'strategy' => 'tag name',
        ],
    ];

    /**
     * @return \Magento\Mtf\Test\Block\TestBlock
     */
    public function getTestBlock()
    {
        return $this->getBlockInstance('testBlock');
    }
}
