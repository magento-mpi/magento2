<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\View;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Custom Options
 * Block of custom options product
 */
class CustomOptions extends Block
{
    /**
     * Field set selector
     *
     * @var string
     */
    protected $fieldsetSelector = '.fieldset';

    /**
     * Row selector
     *
     * @var string
     */
    protected $rowSelector = '.field';

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        $pricePattern = '#\$([\d,]+\.\d+)$#';
        $options = [];
        $index = 1;

        while (($fieldElement = $this->_rootElement->find(
                '//*[@id="product-options-wrapper"]//*[@class="fieldset"]'
                . '/div[not(contains(@class,"downloads")) and contains(@class,"field")][' . $index . ']',
                Locator::SELECTOR_XPATH))
            && $fieldElement->isVisible()
        ) {
            $option = ['price' => []];
            $option['is_require'] = $this->_rootElement->find(
                '//*[@id="product-options-wrapper"]//*[@class="fieldset"]'
                . '/div[not(contains(@class,"downloads")) and contains(@class,"field")'
                . ' and contains(@class,"required")][' . $index . ']',
                Locator::SELECTOR_XPATH
            )->isVisible();
            $option['title'] = $fieldElement->find('.label span:not(.price-notice)')->getText();

            if (($price = $fieldElement->find('.label .price-notice'))
                && $price->isVisible()
            ) {
                $matches = [];
                $value = $price->getText();
                if (preg_match($pricePattern, $value, $matches)) {
                    $option['value'][] = $value;
                    $option['price'][] = $matches[1];
                }
            } elseif (($prices = $fieldElement->find(
                    '//div[contains(@class,"control")]//select',
                    Locator::SELECTOR_XPATH))
                && $prices->isVisible()
            ) {
                $priceIndex = 0;
                while (($price = $prices->find('//option[' . ++$priceIndex . ']', Locator::SELECTOR_XPATH))
                    && $price->isVisible()
                ) {
                    $matches = [];
                    $value = $price->getText();
                    if (preg_match($pricePattern, $value, $matches)) {
                        $option['value'][] = $value;
                        $option['price'][] = $matches[1];
                    }
                }
            }
            $options[$option['title']] = $option;
            ++$index;
        }

        return $options;
    }

    /**
     * Get product custom options
     *
     * @return array
     */
    public function getProductCustomOptions()
    {
        return $this->getOptions('.fieldset > .field');
    }

    /**
     * Get bundle custom options
     *
     * @return array
     */
    public function getBundleCustomOptions()
    {
        return $this->getOptions('#product-options-wrapper > .fieldset > .field');
    }

    /**
     * Get bundle options
     *
     * @return array
     */
    public function getBundleOptions()
    {
        return $this->getOptions('.fieldset.bundle.options > .field');
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
                '//*[*[@class="product options wrapper"]//span[text()="' .
                $attributeLabel .
                '"]]//select',
                Locator::SELECTOR_XPATH,
                'select'
            );
            $select->setValue($attributeValue);
        }
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
            '//*[@class="product options wrapper"]//option[text()="' . $productOption . '"]/..',
            Locator::SELECTOR_XPATH,
            'select'
        );
        $select->setValue($productOption);
    }
}
