<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Class ProductForm
 * Product form on backend product page
 */
class ProductForm extends FormTabs
{
    /**
     * New variation set button selector
     *
     * @var string
     */
    protected $newVariationSet = '[data-ui-id="admin-product-edit-tab-super-config-grid-container-add-attribute"]';

    /**
     * Fill the product form
     *
     * @param FixtureInterface $fixture
     * @param CatalogCategory|null $category
     * @param Element|null $element
     * @return $this
     */
    public function fillProduct(
        FixtureInterface $fixture,
        CatalogCategory $category = null,
        Element $element = null
    ) {
        $tabs = $this->getFieldsByTabs($fixture);
        if ($category) {
            $tabs['product-details']['category_ids']['value'] = $category->getName();
        }

        return parent::fillTabs($tabs, $element);
    }

    /**
     * Fill product variations
     *
     * @param ConfigurableProduct $variations
     * @return void
     */
    public function fillVariations(ConfigurableProduct $variations)
    {
        $variationsBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperConfig(
            $this->_rootElement->find($this->variationsWrapper)
        );
        $variationsBlock->fillAttributeOptions($variations->getConfigurableAttributes());
        $variationsBlock->generateVariations();
        $variationsBlock->fillVariationsMatrix($variations->getVariationsMatrix());
    }
}
