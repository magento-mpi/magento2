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

/**
 * Class Custom Options
 * Form of custom options product
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class CustomOptions extends Form
{
    /**
     * Regexp price pattern
     *
     * @var string
     */
    protected $pricePattern = '#\$([\d,]+\.\d+)$#';

    /**
     * Field set XPath locator
     *
     * @var string
     */
    protected $fieldsetLocator = '//*[@id="product-options-wrapper"]//*[@class="fieldset"]';

    /**
     * Field XPath locator
     *
     * @var string
     */
    protected $fieldLocator = '/div[not(contains(@class,"downloads")) and contains(@class,"field")%s][%d]';

    /**
     * Required field XPath locator
     *
     * @var string
     */
    protected $requiredLocator = ' and contains(@class,"required")';

    /**
     * Select field XPath locator
     *
     * @var string
     */
    protected $selectLocator = './div[contains(@class,"control")]//select';

    /**
     * Title value CSS locator
     *
     * @var string
     */
    protected $titleLocator = '.label span:not(.price-notice)';

    /**
     * Price value CSS locator
     *
     * @var string
     */
    protected $priceLocator = '.label .price-notice';

    /**
     * Option XPath locator
     *
     * @var string
     */
    protected $optionLocator = './option[%d]';

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
    protected $selectByTitleLocator = '//div[label[span[contains(text(),"%s")]]]';

    /**
     * Bundle field CSS locator
     *
     * @var string
     */
    protected $bundleFieldLocator = '#product-options-wrapper > .fieldset > .field';

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $index = 1;

        $fieldElement = $this->_rootElement->find(
            $this->fieldsetLocator . sprintf($this->fieldLocator, '', $index),
            Locator::SELECTOR_XPATH
        );

        while ($fieldElement && $fieldElement->isVisible()) {
            $option = ['price' => []];
            $option['is_require'] = $this->_rootElement->find(
                $this->fieldsetLocator . sprintf($this->fieldLocator, $this->requiredLocator, $index),
                Locator::SELECTOR_XPATH
            )->isVisible();
            $option['title'] = $fieldElement->find($this->titleLocator)->getText();

            if (($price = $fieldElement->find($this->priceLocator))
                && $price->isVisible()
            ) {
                $matches = [];
                $value = $price->getText();
                if (preg_match($this->pricePattern, $value, $matches)) {
                    $option['value'][] = $value;
                    $option['price'][] = $matches[1];
                }
            } elseif (($prices = $fieldElement->find($this->selectLocator, Locator::SELECTOR_XPATH))
                && $prices->isVisible()
            ) {
                $priceIndex = 0;
                while (($price = $prices->find(sprintf($this->optionLocator, ++$priceIndex), Locator::SELECTOR_XPATH))
                    && $price->isVisible()
                ) {
                    $matches = [];
                    $value = $price->getText();
                    if (preg_match($this->pricePattern, $value, $matches)) {
                        $option['value'][] = $value;
                        $option['price'][] = $matches[1];
                    }
                }
            }
            $options[$option['title']] = $option;
            ++$index;
            $fieldElement = $this->_rootElement->find(
                $this->fieldsetLocator . sprintf($this->fieldLocator, '', $index),
                Locator::SELECTOR_XPATH
            );
        }

        return $options;
    }

    /**
     * Fill configurable product options
     *
     * @param array $productOptions
     * @return void
     */
    public function fillProductOptions($productOptions)
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
     * @param string $productOption
     * @return void
     */
    public function selectProductCustomOption($productOption)
    {
        $select = $this->_rootElement->find(
            sprintf($this->optionByValueLocator, $productOption),
            Locator::SELECTOR_XPATH,
            'select'
        );
        $select->setValue($productOption);
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
