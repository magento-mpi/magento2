<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config;

use Mtf\Block\BlockFactory;
use Mtf\Block\Mapper;
use Mtf\Client\Browser;
use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element;
use Magento\Backend\Test\Block\Widget\Form;
use \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute\AttributeSelector;

/**
 * Class Attribute
 * Attribute block in Variation section
 */
class Attribute extends Form
{
    /**
     * The root element of the browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Mapping fields for get values of form
     *
     * @var array
     */
    protected $mappingGetFields = [
        'label' => [
            'selector' => 'td[data-column="name"]',
            'strategy' => Locator::SELECTOR_CSS
        ]
    ];

    /**
     * Variation search block
     *
     * @var string
     */
    protected $variationSearchBlock = '#variations-search-field';

    /**
     * Selector for "Create New Variations Set"
     *
     * @var string
     */
    protected $createNewVariationSet = '[data-ui-id$="add-attribute"]';

    /**
     * New attribute frame selector
     *
     * @var string
     */
    protected $newAttributeFrame = '#create_new_attribute_container';

    /**
     * Selector for "New Attribute" block
     *
     * @var string
     */
    protected $newAttribute = 'body';

    /**
     * Selector for "Save Attribute" button on "New Attribute" dialog window
     *
     * @var string
     */
    protected $saveAttribute = '[data-ui-id="attribute-edit-content-save-button"]';

    /**
     * Selector attribute block by label
     *
     * @var string
     */
    protected $attributeBlockByName = './/*[*/strong[@class="title" and contains(.,"%s")]]';

    /**
     * Selector for attribute block
     *
     * @var string
     */
    protected $attributeBlock = './/div[@id="configurable-attributes-container"]//div[contains(@id,"-wrapper")]';

    /**
     * Selector for "Add Option" button
     *
     * @var string
     */
    protected $addOption = '[role="button"]';

    /**
     * Selector for option container
     *
     * @var string
     */
    protected $optionContainer = '//tr[@data-role="option-container"]';

    /**
     * Selector for option container(row) by number
     *
     * @var string
     */
    protected $optionContainerByNumber = '//tr[@data-role="option-container"][%d]';

    /**
     * Selector for attribute title
     *
     * @var string
     */
    protected $attributeTitle = '.title > span';

    /**
     * Selector for attribute content
     *
     * @var string
     */
    protected $attributeContent = '[id$="-content"]';

    /**
     * Selector for attribute label
     *
     * @var string
     */
    protected $attributeLabel = '[name$="[label]"]';


    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Mapper $mapper, Browser $browser)
    {
        $this->browser = $browser;
        parent::__construct($element, $blockFactory, $mapper);
    }

    /**
     * Fill attributes
     *
     * @param array $attributes
     * @return void
     */
    public function fillAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $isExistAttribute = $this->isExistAttribute($attribute['frontend_label']);

            if (!$isExistAttribute && empty($attribute['attribute_id'])) {
                $this->createNewVariationSet($attribute);
                $this->fillOptions($attribute);
            } else {
                if (!$isExistAttribute) {
                    $this->getAttributeSelector()->setValue($attribute['frontend_label']);
                }
                $this->updateOptions($attribute);
            }
        }
    }

    /**
     * Create new variation set
     *
     * @param array $attribute
     * @return void
     */
    protected function createNewVariationSet(array $attribute)
    {
        $this->_rootElement->find($this->createNewVariationSet)->click();
        $this->browser->switchToFrame(new Locator($this->newAttributeFrame));

        $newAttribute = $this->getEditAttributeForm();
        $newAttribute->getTabElement('properties')->fillFormTab($attribute);
        $newAttribute->_rootElement->find($this->saveAttribute)->click();

        $this->browser->switchToFrame();
    }

    /**
     * Fill options
     *
     * @param array $attribute
     * @return void
     */
    protected function fillOptions(array $attribute)
    {
        $attributeBlock = $this->_rootElement->find(
            sprintf($this->attributeBlockByName, $attribute['frontend_label']),
            Locator::SELECTOR_XPATH
        );
        $count = 0;

        $this->showAttributeContent($attributeBlock);
        if (isset($attribute['label'])) {
            $attributeBlock->find($this->attributeLabel)->setValue($attribute['label']);
        }
        foreach ($attribute['options'] as $option) {
            $attributeBlock->find($this->addOption)->click();
            $count++;
            $optionContainer = $attributeBlock->find(
                sprintf($this->optionContainerByNumber, $count),
                Locator::SELECTOR_XPATH
            );

            $mapping = $this->dataMapping($option);
            $this->_fill($mapping, $optionContainer);
        }
    }

    /**
     * Update options
     *
     * @param array $attribute
     * @return void
     */
    protected function updateOptions(array $attribute)
    {
        $attributeBlock = $this->_rootElement->find(
            sprintf($this->attributeBlockByName, $attribute['frontend_label']),
            Locator::SELECTOR_XPATH
        );
        $count = 0;

        $this->showAttributeContent($attributeBlock);
        if (isset($attribute['label'])) {
            $attributeBlock->find($this->attributeLabel)->setValue($attribute['label']);
        }
        foreach ($attribute['options'] as $option) {
            $count++;
            $optionContainer = $attributeBlock->find(
                sprintf($this->optionContainerByNumber, $count),
                Locator::SELECTOR_XPATH
            );

            if (!$optionContainer->isVisible() && $this->isVisibleOption($attributeBlock, $count-1)) {
                $attributeBlock->find($this->addOption)->click();
            }
            $mapping = $this->dataMapping($option);
            $this->_fill($mapping, $optionContainer);
        }
    }

    /**
     * Check is visible option
     *
     * @param Element $attributeBlock
     * @param int $number
     * @return bool
     */
    protected function isVisibleOption(Element $attributeBlock, $number)
    {
        return $attributeBlock->find(
            sprintf($this->optionContainerByNumber, $number),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }

    /**
     * Get attribute form block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\AttributeForm
     */
    protected function getEditAttributeForm()
    {
        return $this->blockFactory->create(
            'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Edit\AttributeForm',
            ['element' => $this->browser->find($this->newAttribute)]
        );
    }

    /**
     * Get attribute selector element
     *
     * @return AttributeSelector
     */
    public function getAttributeSelector()
    {
        return $this->_rootElement->find(
            $this->variationSearchBlock,
            Locator::SELECTOR_CSS,
            'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute\AttributeSelector'
        );
    }

    /**
     * Get attributes data
     *
     * @return array
     */
    public function getAttributesData()
    {
        $data = [];
        $attributeBlocks = $this->_rootElement->find($this->attributeBlock, Locator::SELECTOR_XPATH)->getElements();
        $optionMapping = $this->dataMapping();

        foreach ($attributeBlocks as $attributeBlock) {
            $attribute = [
                'frontend_label' => $attributeBlock->find($this->attributeTitle)->getText(),
                'label' => $attributeBlock->find($this->attributeLabel)->getValue(),
                'options' => []
            ];

            /** @var Element $attributeBlock */
            $this->showAttributeContent($attributeBlock);
            $options = $attributeBlock->find($this->optionContainer, Locator::SELECTOR_XPATH)->getElements();
            foreach ($options as $optionKey => $option) {
                /** @var Element $option */
                if ($option->isVisible()) {
                    $attribute['options'][$optionKey] = $this->_getData($optionMapping, $option);
                    $attribute['options'][$optionKey] += $this->getOptionalFields($option);
                }
            }

            $data[] = $attribute;
        }
        return $data;
    }

    /**
     * Show attribute content
     *
     * @param Element $attribute
     * @return void
     */
    protected function showAttributeContent(Element $attribute)
    {
        if (!$attribute->find($this->attributeContent)->isVisible()) {
            $attribute->find($this->attributeTitle)->click();

            $browser = $attribute;
            $selector = $this->attributeContent;
            $browser->waitUntil(
                function () use ($browser, $selector) {
                    return $browser->find($selector)->isVisible() ? true : null;
                }
            );
        }
    }

    /**
     * Check exist attribute by label
     *
     * @param string $label
     * @return bool
     */
    protected function isExistAttribute($label)
    {
        return $this->_rootElement->find(
            sprintf($this->attributeBlockByName, $label),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }

    /**
     * Get optional fields
     *
     * @param Element $context
     * @param array $fields
     * @return array
     */
    protected function getOptionalFields(Element $context, array $fields = [])
    {
        $data = [];

        $fields = empty($fields) ? $this->mappingGetFields : $fields;
        foreach ($fields as $name => $params) {
            $data[$name] = $context->find($params['selector'], $params['strategy'])->getText();
        }
        return $data;
    }
}
