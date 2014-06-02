<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Block\Catalog\Product\View;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Links
 *
 * Downloadable links blocks on frontend
 */
class Links extends Block
{
    /**
     * Selector title for for links
     *
     * @var string
     */
    protected $titleForLink = '//div[contains(@class,"field downloads")]/label[@class="label"]/span';

    /**
     * Format for downloadable links list selector
     *
     * @var string
     */
    protected $linksListSelector = '//*[@id="downloadable-links-list"]/div[%d]/';

    /**
     * Title selector item links
     *
     * @var string
     */
    protected $titleForList = "label[@class='label']/span[1]";

    /**
     * Price selector item links
     *
     * @var string
     */
    protected $priceForList = 'label/span[contains(@class,"price-container")]//span[@class="price"]';

    /**
     * Checkbox selector item links
     *
     * @var string
     */
    protected $separatelyForList = "input[@type='checkbox']";

    /**
     * Change format downloadable links list
     *
     * @param int $index
     * @return string
     */
    protected function formatIndex($index)
    {
        return sprintf($this->linksListSelector, $index);
    }

    /**
     * Get title for links block
     *
     * @return string
     */
    public function getTitleForLinkBlock()
    {
        return $this->_rootElement->find($this->titleForLink, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get title for item link on data list
     *
     * @param int $index
     * @return string
     */
    public function getItemTitle($index)
    {
        return $this->_rootElement->find($this->formatIndex($index) . $this->titleForList,Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Visible checkbox for item link on data list
     *
     * @param int $index
     * @return bool
     */
    public function isVisibleItemCheckbox($index)
    {
        return $this->_rootElement->find($this->formatIndex($index) . $this->separatelyForList, Locator::SELECTOR_XPATH)
            ->isVisible();
    }

    /**
     * Get price for item link on data list
     *
     * @param int $index
     * @return string
     */
    public function getItemPrice($index)
    {
        return $this->_rootElement->find($this->formatIndex($index) . $this->priceForList, Locator::SELECTOR_XPATH)
            ->getText();
    }
}
