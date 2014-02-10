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
use Mtf\Fixture\DataFixture;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductActionAttributeEdit
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductActionAttributeEdit extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product_action_attribute/edit';

    /**
     * @var string
     */
    protected $_attributesFormBlock = 'body';

    /**
     * @return \Magento\Catalog\Test\Block\Backend\Product\Attribute\MassAction\Edit
     */
    public function getAttributesBlockForm()
    {
        return Factory::getBlockFactory()->getMagentoCatalogBackendProductAttributeMassActionEdit(
            $this->_browser->find($this->_attributesFormBlock, Locator::SELECTOR_CSS)
        );
    }
}
