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
    protected $required = './self::*[contains(@class,"required")]';

    /**
     * Selector for price notice of option
     *
     * @var string
     */
    protected $priceNotice = './/*[@class="price-notice"]';

    /**
     * Selector for max characters of option
     *
     * @var string
     */
    protected $maxCharacters = './/div[@class="control"]/p[@class="note"]/strong';

    /**
     * Selector for label of option value element
     *
     * @var string
     */
    protected $optionLabel = './/div[@class="control"]//label[contains(@for, "options_")][%d]';

    /**
     * Select note of option by number
     *
     * @var string
     */
    protected $noteByNumber = './/*[@class="note"][%d]/strong';

    /**
     * Selector for select element of option
     *
     * @var string
     */
    protected $selectOption = './/div[@class="control"]/select';

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
    protected $optionByValueLocator = '//*[@class="product-options-wrapper"]//option[text()="%s"]/..';

    /**
     * Select XPath locator by title
     *
     * @var string
     */
    protected $selectByTitleLocator = '//*[*[@class="product-options-wrapper"]//span[text()="%s"]]//select';

    /**
     * Get product options
     *
     * @param FixtureInterface|null $product [optional]
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
        $price = $this->getOptionPriceNotice($option);
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
     * Get data of "Area" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getAreaData(Element $option)
    {
        return $this->getFieldData($option);
    }

    /**
     * Get data of "File" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getFileData(Element $option)
    {
        $price = $this->getOptionPriceNotice($option);

        return [
            'options' => [
                [
                    'price' => floatval($price),
                    'file_extension' => $this->getOptionNotice($option, 1),
                    'image_size_x' => preg_replace('/[^0-9]/', '', $this->getOptionNotice($option, 2)),
                    'image_size_y' => preg_replace('/[^0-9]/', '', $this->getOptionNotice($option, 3)),
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
        // Skip "Choose option ..."(option #1)
        return $this->getSelectData($option, 2);
    }

    /**
     * Get data of "Multiple Select" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getMultipleSelectData(Element $option)
    {
        return $this->getSelectData($option, 1);
    }

    /**
     * Get data of "Radio Buttons" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getRadioButtonsData(Element $option)
    {
        $listOptions = [];

        $count = 1;
        $option = $option->find(sprintf($this->optionLabel, $count), Locator::SELECTOR_XPATH);
        while ($option->isVisible()) {
            $listOptions[] = $this->parseOptionText($option->getText());
            ++$count;
            $option = $option->find(sprintf($this->optionLabel, $count), Locator::SELECTOR_XPATH);
        }

        return [
            'options' => $listOptions
        ];
    }

    /**
     * Get data of "Checkbox" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getCheckboxData(Element $option)
    {
        return $this->getRadioButtonsData($option);
    }

    /**
     * Get data of "Date" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getDateData(Element $option)
    {
        $price = $this->getOptionPriceNotice($option);

        return [
            'options' => [
                [
                    'price' => floatval($price)
                ]
            ]
        ];
    }

    /**
     * Get data of "Date & Time" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getDateTimeData(Element $option)
    {
        return $this->getDateData($option);
    }

    /**
     * Get data of "Time" custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getTimeData(Element $option)
    {
        return $this->getDateData($option);
    }

    /**
     * Get data of select custom option
     *
     * @param Element $option
     * @param int $firstElement
     * @return array
     */
    protected function getSelectData(Element $option, $firstElement = 1)
    {
        $select = $option->find($this->selectOption, Locator::SELECTOR_XPATH, 'select');
        $listOptions = [];

        $count = $firstElement;
        $selectOption = $select->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        while ($selectOption->isVisible()) {
            $listOptions[] = $this->parseOptionText($selectOption->getText());
            ++$count;
            $selectOption = $select->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        }

        return [
            'options' => $listOptions
        ];
    }

    /**
     * Get price from price-notice of custom option
     *
     * @param Element $option
     * @return array
     */
    protected function getOptionPriceNotice(Element $option)
    {
        $priceNotice = $option->find($this->priceNotice, Locator::SELECTOR_XPATH);
        if (!$priceNotice->isVisible()) {
            return null;
        }
        return preg_replace('/[^0-9\.]/', '', $priceNotice->getText());
    }

    /**
     * Get notice of option by number
     *
     * @param Element $option
     * @param int $number
     * @return mixed
     */
    protected function getOptionNotice(Element $option, $number)
    {
        $note = $option->find(sprintf($this->noteByNumber, $number), Locator::SELECTOR_XPATH);
        return $note->isVisible() ? $note->getText() : null;
    }

    /**
     * Parse option text to title and price
     *
     * @param $optionText
     * @return array
     */
    protected function parseOptionText($optionText) {
        preg_match('/\+?.([0-9.]+)$/', $optionText, $match);
        $optionPrice = isset($match[1]) ? $match[1] : 0;
        $optionTitle = isset($match[0])
            ? substr($optionText, 0, strlen($optionText) - strlen($match[0]))
            : $optionText;

        return [
            'title' => trim($optionTitle),
            'price' => $optionPrice
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
        $type = preg_replace('/[^a-zA-Z]/', '', $optionName);
        return ucfirst(strtolower($type));
    }
}
