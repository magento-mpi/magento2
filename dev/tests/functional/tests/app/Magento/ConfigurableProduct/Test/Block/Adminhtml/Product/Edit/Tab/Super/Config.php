<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class Config
 * Adminhtml catalog super product configurable tab
 */
class Config extends Tab
{
    /**
     * Selector for trigger show/hide "Variations" tab
     *
     * @var string
     */
    protected $variationsTabTrigger = '[data-panel="product-variations"] .title';

    /**
     * Selector for content "Variations" tab
     *
     * @var string
     */
    protected $variationsTabContent = '#super_config-content';

    /**
     * Selector for button "Generate Variations"
     *
     * @var string
     */
    protected $generateVariations = '[data-ui-id="product-variations-generator-generate"]';

    /**
     * Selector for variations matrix
     *
     * @var string
     */
    protected $variationsMatrix = '[data-role="product-variations-matrix"]';

    /**
     * Fill variations fieldset
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $attributes = isset($fields['configurable_attributes_data']['value'])
            ? $fields['configurable_attributes_data']['value']
            : [];

        $this->showContent();

        if (!empty($attributes['attributes_data'])) {
            $this->getAttributeBlock()->fillAttributes($attributes['attributes_data']);
        }
        if (!empty($attributes['matrix'])) {
            $this->generateVariations();
            $this->getVariationsBlock()->fillVariations($attributes['matrix']);
        }

        return $this;
    }

    /**
     * Show "Variations" tab content
     *
     * @return void
     */
    public function showContent()
    {
        $content = $this->_rootElement->find($this->variationsTabContent);
        if (!$content->isVisible()) {
            $this->_rootElement->find($this->variationsTabTrigger)->click();
            $this->waitForElementVisible($this->variationsTabContent);
        }
    }

    /**
     * Generate variations
     *
     * @return void
     */
    public function generateVariations()
    {
        $this->_rootElement->find($this->generateVariations)->click();
        $this->waitForElementVisible($this->variationsMatrix);
    }

    /**
     * Get block of attributes
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute
     */
    public function getAttributeBlock()
    {
        return $this->blockFactory->create(
            '\Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute',
            ['element' => $this->_rootElement]
        );
    }

    /**
     * Get block of variations
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix
     */
    public function getVariationsBlock()
    {
        return $this->blockFactory->create(
            '\Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix',
            ['element' => $this->_rootElement->find($this->variationsMatrix)]
        );
    }

    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = [];

        $this->showContent();
        $data['attributes_data'] = $this->getAttributeBlock()->getAttributesData();
        $data['matrix'] = $this->getVariationsBlock()->getVariationsData();

        return ['configurable_attributes_data' => $data];
    }
}
