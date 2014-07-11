<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\View;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Custom Options
 * Form of custom options product
 */
class CustomOptions extends Form
{
    /**
     * Selector for options context
     */
    protected $optionsContext = '#product-options-wrapper > fieldset';

    /**
     * Selector for single option block
     *
     * @var string
     */
    protected $optionElement = './div[contains(@class,"field")][%d]';

    /**
     * Selector for title of option
     *
     * @var string
     */
    protected $title = './label/span[1]';

    /**
     * Selector for required option
     *
     * @var string
     */
    protected $required = '//self::*[contains(@class,"required")]';

    /**
     * Selector for price notice of "Field" option
     *
     * @var string
     */
    protected $priceNotice = './/*[@class="price-notice"]';

    /**
     * Selector for max characters of "Field" option
     *
     * @var string
     */
    protected $maxCharacters = './/div[@class="control"]/p[@class="note"]/strong';

    /**
     * Selector for select element of drop-down option
     *
     * @var string
     */
    protected $dropdownSelect = './/div[@class="control"]/select';

    /**
     * Selector for option of select element
     *
     * @var string
     */
    protected $option = './/option[%d]';

    /**
     * Option XPath locator by value
     *
     * @var string
     */
    protected $optionByValueLocator = '//*[@class="product options wrapper"]//option[contains(text(),"%s")]/..';

    /**
     * Select XPath locator by title
     *
     * @var string
     */
    protected $selectByTitleLocator = '//div[label[span[contains(text(),"%s")]]]';

    /**
     * Get product options
     *
     * @param FixtureInterface|null $product
     * @return array
     * @throws \Exception
     */
    public function getOptions(FixtureInterface $product = null)
    {
        $dataOptions = ($product && $product->hasData('custom_options')) ? $product->getCustomOptions() : [];
        $listCustomOptions = $this->getListCustomOptions();
        $readyOptions = [];
        $result = [];

        foreach ($dataOptions as $option) {
            $title = $option['title'];
            if (!isset($listCustomOptions[$title])) {
                throw new \Exception("Can't find option: \"{$title}\"");
            }

            /** @var Element $optionElement */
            $optionElement = $listCustomOptions[$title];
            $typeMethod = preg_replace('/[^a-zA-Z]/', '', $option['type']);
            $getTypeData = 'get' . ucfirst(strtolower($typeMethod)) . 'Data';

            $optionData = $this->$getTypeData($optionElement);
            $optionData['title'] = $title;
            $optionData['type'] = $option['type'];
            $optionData['is_require'] = $optionElement->find($this->required, Locator::SELECTOR_XPATH)->isVisible()
                ? 'Yes'
                : 'No';

            $readyOptions[] = $title;
            $result[$title] = $optionData;
        }

        $unreadyCustomOptions = array_diff_key($listCustomOptions, array_flip($readyOptions));
        foreach ($unreadyCustomOptions as $optionElement) {
            $title = $optionElement->find($this->title, Locator::SELECTOR_XPATH)->getText();
            $result[$title] = ['title' => $title];
        }

        return $result;
    }

    /**
     * Get list custom options
     *
     * @return array
     */
    protected function getListCustomOptions()
    {
        $customOptions = [];
        $context = $this->_rootElement->find($this->optionsContext);

        $count = 1;
        $optionElement = $context->find(sprintf($this->optionElement, $count), Locator::SELECTOR_XPATH);
        while ($optionElement->isVisible()) {
            $title = $optionElement->find($this->title, Locator::SELECTOR_XPATH)->getText();
            $customOptions[$title] = $optionElement;
            ++$count;
            $optionElement = $context->find(sprintf($this->optionElement, $count), Locator::SELECTOR_XPATH);
        }
        return $customOptions;
    }

    /**
     * Get data of "Field" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getFieldData(Element $option)
    {
        $priceNotice = $option->find($this->priceNotice, Locator::SELECTOR_XPATH)->getText();
        $price = preg_replace('/[^0-9\.]/', '', $priceNotice);
        $maxCharacters = $option->find($this->maxCharacters, Locator::SELECTOR_XPATH);

        return [
            'options' => [
                [
                    'price' => floatval($price),
                    'max_characters' => $maxCharacters->isVisible() ? $maxCharacters->getText() : null,
                ]
            ]
        ];
    }

    /**
     * Get data of "Drop-down" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getDropdownData(Element $option)
    {
        $select = $option->find($this->dropdownSelect, Locator::SELECTOR_XPATH, 'select');
        $listSelectOptions = [];

        $count = 2;
        $selectOption = $select->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        while ($selectOption->isVisible()) {
            $optionData = explode('+', $selectOption->getText());
            $listSelectOptions[] = [
                'title' => trim($optionData[0]),
                'price' => isset($optionData[1]) ? preg_replace('/[^0-9\.]/', '', $optionData[1]) : '',
            ];

            ++$count;
            $selectOption = $select->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        }

        return [
            'options' => $listSelectOptions
        ];
    }

    /**
     * Fill configurable product options
     *
     * @param array $productOptions
     * @return void
     */
    public function fillProductOptions(array $productOptions)
    {
        foreach ($productOptions as $attributeLabel => $attributeValue) {
            $select = $this->_rootElement->find(
                sprintf($this->selectByTitleLocator, $attributeLabel),
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($attributeValue);
        }
    }

    /**
     * Fill custom options
     *
     * @param array $customOptions
     * @return void
     */
    public function fillCustomOptions(array $customOptions)
    {
        $type = $this->optionNameConvert($customOptions['type']);
        $customOptions += $this->dataMapping([$type => '']);

        $isDate = $customOptions['type'] == 'Date' ||
            $customOptions['type'] == 'Time' ||
            $customOptions['type'] == 'Date & Time';
        $isChecked = $customOptions['type'] == 'Checkbox' || $customOptions['type'] == 'Radio Buttons';

        if ($isDate) {
            $customOptions['value'] = explode('/', $customOptions['value'][0]);
            $customOptions['dateSelector'] = $this->setDateTypeSelector(count($customOptions['value']));
        }

        foreach ($customOptions['value'] as $key => $attributeValue) {
            $selector = $customOptions[$type]['selector'];
            if ($isDate) {
                $selector .= $customOptions['dateSelector'][$key];
            } elseif ($isChecked) {
                $selector = str_replace('%product_name%', $attributeValue, $selector);
                $attributeValue = 'Yes';
            }

            $select = $this->_rootElement->find(
                sprintf($this->selectByTitleLocator, $customOptions['title']) . $selector,
                Locator::SELECTOR_XPATH,
                $customOptions[$type]['input']
            );
            $select->setValue($attributeValue);
        }
    }

    /**
     * Set item data type selector
     *
     * @param int $count
     * @return array
     */
    protected function setDateTypeSelector($count)
    {
        $result = [];
        $parent = '';
        for ($i = 0; $i < $count; $i++) {
            if (!(($i + 1) % 4)) {
                $parent = '//span';
            }
            $result[$i] = $parent . '//select[' . ($i % 3 + 1) . ']';
        }

        return $result;
    }

    /**
     * Choose custom option in a drop down
     *
     * @param string $title
     * @param string|null $value [optional]
     * @return void
     */
    public function selectProductCustomOption($title, $value = null)
    {
        $select = $this->_rootElement->find(
            sprintf($this->selectByTitleLocator, $title),
            Locator::SELECTOR_XPATH,
            'select'
        );

        if (null === $value) {
            $value = $select->find('.//option[@value != ""][1]', Locator::SELECTOR_XPATH)->getText();
        }
        $select->setValue($value);
    }

    /**
     * Convert option name
     *
     * @param string $optionName
     * @return string
     */
    protected function optionNameConvert($optionName)
    {
        $optionName = str_replace(' & ', '', $optionName);
        if ($end = strpos($optionName, ' ')) {
            $optionName = substr($optionName, 0, $end);
        } elseif ($end = strpos($optionName, '-')) {
            $optionName = substr($optionName, 0, $end) . ucfirst(substr($optionName, ($end + 1)));
        }
        return lcfirst($optionName);
    }
}
