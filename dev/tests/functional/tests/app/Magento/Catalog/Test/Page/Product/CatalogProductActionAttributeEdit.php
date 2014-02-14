<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductActionAttributeEdit
 *
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductActionAttributeEdit extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product_action_attribute/edit';

    /**
     * CSS selector for attributes form block
     *
     * @var string
     */
    protected $attributesFormBlock = 'body';

    /**
     * Retrieve attributes form block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Action\Attribute
     */
    public function getAttributesBlockForm()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditActionAttribute(
            $this->_browser->find($this->attributesFormBlock, Locator::SELECTOR_CSS)
        );
    }
}
