<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Test\Block\Catalog\Layer;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Product View block
 *
 * @package Magento\Search\Test\Block\Catalog\Layer
 */
class View extends Block
{
    /**
     * 'Clear All' link
     *
     * @var string
     */
    protected $clearAll = '.action.reset';

    /**
     * Price range
     *
     * @var string
     */
    protected $priceRange = "[href$='?price=%s']";

    /**
     * Attribute option
     *
     * @var string
     */
    protected $attributeOption = "//a[contains(text(), '%s')]";


    /**
     * Click on 'Clear All' link
     */
    public function clearAll()
    {
        $this->reinitRootElement();
        $this->_rootElement->find($this->clearAll)->click();
    }

    /**
     * Select product price range
     *
     * @param string $range
     */
    public function selectPriceRange($range)
    {
        $this->_rootElement->find(sprintf($this->priceRange, $range))->click();
    }

    /**
     * Select attribute option
     *
     * @param string $optionName
     */
    public function selectAttributeOption($optionName)
    {
        $this->reinitRootElement();
        $this->_rootElement->find(sprintf($this->attributeOption, $optionName), Locator::SELECTOR_XPATH)->click();
    }
}