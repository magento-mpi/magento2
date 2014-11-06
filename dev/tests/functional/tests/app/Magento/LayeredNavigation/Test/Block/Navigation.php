<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\LayeredNavigation\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Catalog layered navigation view block
 */
class Navigation extends Block
{
    /**
     * 'Clear All' link.
     *
     * @var string
     */
    protected $clearAll = '.action.clear';

    /**
     * Price range.
     *
     * @var string
     */
    protected $priceRange = "[href$='?price=%s']";

    /**
     * Attribute option.
     *
     * @var string
     */
    protected $attributeOption = "//a[contains(text(), '%s')]";

    /**
     * Attribute option title selector.
     *
     * @var string
     */
    protected $optionTitle = '.filter-options-title';

    /**
     * Attribute option content selector.
     *
     * @var string
     */
    protected $optionContent = '.filter-options-content';

    /**
     * Click on 'Clear All' link.
     *
     * @return void
     */
    public function clearAll()
    {
        $this->reinitRootElement();
        $this->_rootElement->find($this->clearAll, locator::SELECTOR_CSS)->click();
    }

    /**
     * Select product price range.
     *
     * @param string $range
     * @return void
     */
    public function selectPriceRange($range)
    {
        $this->reinitRootElement();
        $this->_rootElement->find(sprintf($this->priceRange, $range))->click();
    }

    /**
     * Select attribute option.
     *
     * @param string $optionName
     * @return void
     */
    public function selectAttributeOption($optionName)
    {
        $this->reinitRootElement();
        $this->_rootElement->find(sprintf($this->attributeOption, $optionName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get array of available options.
     *
     * @return array
     */
    public function getAvailableOptions()
    {
        $this->reinitRootElement();
        $options = $this->_rootElement->find($this->optionTitle)->getElements();
        $contents = $this->_rootElement->find($this->optionContent)->getElements();

        $data = [];
        foreach ($options as $key => $option) {
            $data[$option->getText()] = $contents[$key];
        }
        return $data;
    }
}
