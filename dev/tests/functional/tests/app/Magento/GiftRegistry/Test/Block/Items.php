<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class Items
 * Frontend gift registry items
 */
class Items extends Block
{
    /**
     * Product name selector in registry items grid
     *
     * @var string
     */
    protected $productName = '//a[contains(text(), "%s")]';

    /**
     * Update GiftRegistry button selector
     *
     * @var string
     */
    protected $updateGiftRegistry = '.action.update';

    /**
     * Product row selector
     *
     * @var string
     */
    protected $productRow = '//tr[td[contains(@class,"product") and a[contains(.,"%s")]]]';

    /**
     * Update giftRegistry fields selectors
     *
     * @var array
     */
    protected $updateFields = [
        'note' => [
            'selector' => '[name$="[note]"]',
            'input' => null
        ],
        'qty' => [
            'selector' => '[name$="[qty]"]',
            'input' => null
        ],
        'delete' => [
            'selector' => '[name$="[delete]"]',
            'input' => 'checkbox'
        ]
    ];

    /**
     * Info message selector
     *
     * @var string
     */
    protected $infoMessage = '//div[contains(@class, "message info")]/span';

    /**
     * Is visible product in gift registry items grid
     *
     * @param string $name
     * @return bool
     */
    public function isProductInGrid($name)
    {
        return $this->_rootElement->find(sprintf($this->productName, $name), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Update GiftRegistry
     *
     * @return void
     */
    public function updateGiftRegistry()
    {
        $this->_rootElement->find($this->updateGiftRegistry)->click();
    }

    /**
     * Fill update giftRegistry item form
     *
     * @param CatalogProductSimple $product
     * @param array $options
     * @return void
     */
    public function fillUpdateForm(CatalogProductSimple $product, array $options)
    {
        $productRowSelector = sprintf($this->productRow, $product->getName());
        $productRow = $this->_rootElement->find($productRowSelector, Locator::SELECTOR_XPATH);
        foreach ($options as $field => $value) {
            if ($value !== '-') {
                $productRow->find(
                    $this->updateFields[$field]['selector'],
                    Locator::SELECTOR_CSS,
                    $this->updateFields[$field]['input']
                )->setValue($value);
            }
        }
    }

    /**
     * Get info message
     *
     * @return string
     */
    public function getInfoMessage()
    {
        return $this->_rootElement->find($this->infoMessage, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get item data from form
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function getItemFormData(CatalogProductSimple $product)
    {
        $data = [];
        $productRowSelector = sprintf($this->productRow, $product->getName());
        $productRow = $this->_rootElement->find($productRowSelector, Locator::SELECTOR_XPATH);
        foreach ($this->updateFields as $field => $locator) {
            $data[$field] = $productRow->find(
                $locator['selector'],
                Locator::SELECTOR_CSS,
                $locator['input']
            )->getValue();
        }

        return $data;
    }
}
