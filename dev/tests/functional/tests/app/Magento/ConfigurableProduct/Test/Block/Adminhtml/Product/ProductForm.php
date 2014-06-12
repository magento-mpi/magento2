<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product;

use Mtf\Block\Mapper;
use Mtf\Client\Element;
use Mtf\Client\Browser;
use Mtf\Factory\Factory;
use Mtf\Util\XmlConverter;
use Mtf\Block\BlockFactory;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Block\Adminhtml\Product\Form;

/**
 * Class ProductForm
 * Product creation form
 */
class ProductForm extends Form
{
    /**
     * New attribute selector
     *
     * @var string
     */
    protected $newAttribute = 'body';

    /**
     * New attribute frame selector
     *
     * @var string
     */
    protected $newAttributeFrame = '#create_new_attribute_container';

    /**
     * Variations tab selector
     *
     * @var string
     */
    protected $productDetailsTab = '#product_info_tabs_product-details';

    /**
     * Choose affected attribute set dialog popup window
     *
     * @var string
     */
    protected $affectedAttributeSet = "//div[div/@data-id='affected-attribute-set-selector']";

    /**
     * Variations tab selector
     *
     * @var string
     */
    protected $variationsTab = '[data-ui-id="product-tabs-tab-content-super-config"] .title';

    /**
     * Variations wrapper selector
     *
     * @var string
     */
    protected $variationsWrapper = '[data-ui-id="product-tabs-tab-content-super-config"]';

    /**
     * @param Element $element
     * @param Mapper $mapper
     * @param XmlConverter $xmlConverter
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     */
    public function __construct(
        Element $element,
        Mapper $mapper,
        XmlConverter $xmlConverter,
        BlockFactory $blockFactory,
        Browser $browser
    ) {
        $this->browser = $browser;
        parent::__construct($element, $mapper, $blockFactory, $xmlConverter);
    }

    /**
     * Get choose affected attribute set dialog popup window
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Backend\Product\AffectedAttributeSet
     */
    protected function getAffectedAttributeSetBlock()
    {
        return Factory::getBlockFactory()->getMagentoConfigurableProductBackendProductAffectedAttributeSet(
            $this->_rootElement->find($this->affectedAttributeSet, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Get attribute edit block
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Backend\Product\Attribute\Edit
     */
    public function getConfigurableAttributeEditBlock()
    {
        $this->browser->switchToFrame(new Locator($this->newAttributeFrame));
        return Factory::getBlockFactory()->getMagentoConfigurableProductBackendProductAttributeEdit(
            $this->browser->find($this->newAttribute, Locator::SELECTOR_TAG_NAME)
        );
    }

    /**
     * Initialization categories before use in the form of
     *
     * @param CatalogCategory $category
     * @return void
     */
    public function setCategory(CatalogCategory $category)
    {
        $this->category = $category;
    }

    /**
     * Fill the product form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $this->fillCategory($fixture);
        parent::fill($fixture);
        if ($fixture->getAttributeOptions()) {
            $this->_rootElement->find($this->productDetailsTab)->click();
            $this->clickCreateNewVariationSet();
            $attributeBlockForm = $this->getConfigurableAttributeEditBlock();
            $attributeBlockForm->fillAttributeOption($fixture->getAttributeOptions());
        }
        if ($fixture->getConfigurableOptions()) {
            $this->browser->switchToFrame();
            $this->variationsFill($fixture->getConfigurableOptions());
        }

    }

    /**
     * Save product
     *
     * @param FixtureInterface $fixture
     * @return \Magento\Backend\Test\Block\Widget\Form|void
     */
    public function save(FixtureInterface $fixture = null)
    {
        parent::save($fixture);
        if ($this->getAffectedAttributeSetBlock()->isVisible()) {
            $this->getAffectedAttributeSetBlock()->chooseAttributeSet($fixture);
        }
    }

    /**
     * Get variations block
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config
     */
    protected function getVariationsBlock()
    {
        return Factory::getBlockFactory()->getMagentoConfigurableProductAdminhtmlProductEditTabSuperConfig(
            $this->browser->find($this->variationsWrapper)
        );
    }

    /**
     * Fill product variations
     *
     * @param array $variations
     */
    public function variationsFill(array $variations)
    {
        $variationsBlock = $this->getVariationsBlock();
        $variationsBlock->fillAttributeOptions($variations);
        $variationsBlock->generateVariations();
    }

    /**
     * Open variations tab
     */
    public function openVariationsTab()
    {
        $this->_rootElement->find($this->variationsTab)->click();
    }

    /**
     * Click on 'Create New Variation Set' button
     */
    public function clickCreateNewVariationSet()
    {
        $this->_rootElement->find($this->newVariationSet)->click();
    }

    /**
     * Find Attribute on Product page
     *
     * @param string $attributeName
     * @return bool
     */
    public function findAttribute($attributeName)
    {
        $this->openTab('product-details');

        return $this->getVariationsBlock()->getAttributeBlock($attributeName)->isVisible();
    }
}
