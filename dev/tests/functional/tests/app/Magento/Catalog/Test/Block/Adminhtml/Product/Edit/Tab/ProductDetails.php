<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit;

/**
 * Product Details Tab.
 */
class ProductDetails extends Tab
{
    /**
     * Selector for 'New Attribute' button.
     *
     * @var string
     */
    protected $newAttributeButton = '[id^="create_attribute"]';

    /**
     * New attribute form selector.
     *
     * @var string
     */
    protected $newAttributeForm = '#create_new_attribute';

    /**
     * Magento loader.
     *
     * @var string
     */
    protected $loader = '[data-role="loader"]';

    /**
     * Attribute Search locator the Product page.
     *
     * @var string
     */
    protected $attributeSearch = '#product-attribute-search-container';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['custom_attribute'])) {
            /** @var CatalogProductAttribute $attribute */
            $attribute = $fields['custom_attribute']['source']->getAttribute();
            if (!$attribute->hasData('attribute_id')) {
                $this->fillAttributeForm($attribute);
                $this->reinitRootElement();
            }
        }
        parent::fillFormTab($fields, $element);
    }

    /**
     * Fixture mapping.
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $mappingFields = ($parent !== null) ? $parent : $this->mapping;
        $mapping = parent::dataMapping($fields, $parent);
        if (isset($mapping['custom_attribute'])) {
            $mapping['custom_attribute']['selector'] = sprintf(
                $mappingFields['custom_attribute']['selector'],
                $fields['custom_attribute']['value']['code']
            );
        }
        return $mapping;
    }

    /**
     * Click on 'New Attribute' button.
     *
     * @return void
     */
    public function addNewAttribute()
    {
        $this->_rootElement->find($this->attributeSearch)->click();
        $this->_rootElement->find($this->newAttributeButton)->click();
    }

    /**
     * Fill product attribute form.
     *
     * @param CatalogProductAttribute $productAttribute
     * @return void
     */
    public function fillAttributeForm(CatalogProductAttribute $productAttribute)
    {
        $this->addNewAttribute();

        $browser = $this->browser;
        $element = $this->newAttributeForm;
        $loader = $this->loader;

        $attributeForm = $this->getAttributeForm();
        $attributeForm->fill($productAttribute);

        $this->_rootElement->waitUntil(
            function () use ($browser, $element) {
                return $browser->find($element)->isVisible() == false ? true : null;
            }
        );

        $this->_rootElement->waitUntil(
            function () use ($browser, $loader) {
                return $browser->find($loader)->isVisible() == false ? true : null;
            }
        );
    }

    /**
     * Get Attribute Form.
     *
     * @return Edit
     */
    public function getAttributeForm()
    {
        /** @var Edit $attributeForm */
        return $this->blockFactory->create(
            'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit',
            ['element' => $this->browser->find('body')]
        );
    }
}
