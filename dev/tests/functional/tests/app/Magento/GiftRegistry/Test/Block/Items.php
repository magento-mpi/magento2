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
use Mtf\Fixture\FixtureFactory;

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
     * Item row selector
     *
     * @var string
     */
    protected $itemRow = '//tr[td[contains(@class,"product") and a[contains(.,"%s")]]]';

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
     * Get info message
     *
     * @return string
     */
    public function getInfoMessage()
    {
        return $this->_rootElement->find($this->infoMessage, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Gift Registry item form block
     *
     * @param CatalogProductSimple $item
     * @return \Magento\GiftRegistry\Test\Block\Items\ItemForm
     */
    protected function getItemForm(CatalogProductSimple $item)
    {
        return $this->blockFactory->create(
            'Magento\GiftRegistry\Test\Block\Items\ItemForm',
            ['element' => $this->_rootElement->find(sprintf($this->itemRow, $item->getName()), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Fill Gift Registry item form
     *
     * @param CatalogProductSimple $item
     * @param array $updateOptions
     * @return void
     */
    public function fillItemForm(CatalogProductSimple $item, $updateOptions)
    {
        $this->getItemForm($item)->fillForm($updateOptions);
    }

    /**
     * Get Gift Registry item form data
     *
     * @param CatalogProductSimple $item
     * @return array
     */
    public function getItemData(CatalogProductSimple $item)
    {
        return $this->getItemForm($item)->getData();
    }
}
