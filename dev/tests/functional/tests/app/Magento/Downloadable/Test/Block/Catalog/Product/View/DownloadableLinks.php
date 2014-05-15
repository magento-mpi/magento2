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
 * Class DownloadableLinks
 * for get links blocks on frontend
 */
class DownloadableLinks extends Block
{
    /**
     * Title for for links
     *
     * @var string
     */
    protected $downloadLinksDataTitleForForLink = '//div[contains(@class,"field downloads")]/label[@class="label"]/span';

    /**
     * Title item links
     *
     * @var string
     */
    protected $downloadLinksDataTitleForList = "//*[@id='downloadable-links-list']/div[%d]/label[@class='label']/span[1]";

    /**
     * Price item links
     *
     * @var string
     */
    protected $downloadLinksDataPriceForList = '//*[@id="downloadable-links-list"]/div[%d]/label/span[contains(@class,"price-container")]//span[@class="price"]';

    /**
     * Checkbox item links
     *
     * @var string
     */
    protected $downloadLinksDataCheckboxForList = "//*[@id='downloadable-links-list']/div[%d]/input[@type='checkbox']";

    /**
     * Text for for links data
     * @return string
     */
    public function getDownloadableLinksDataTitleForForLink()
    {
        return $this->_rootElement->find($this->downloadLinksDataTitleForForLink, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * @param $index
     * Text for link on data list
     * @return string
     */
    public function getDownloadableLinksDataTitleForList($index)
    {
        $formatTitle = sprintf($this->downloadLinksDataTitleForList, $index);
        return $this->_rootElement->find($formatTitle, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * @param $index
     * Checkbox for link on data list
     * @return bool
     */
    public function getDownloadableLinksDataCheckboxForList($index)
    {
        $formatCheckbox = sprintf($this->downloadLinksDataCheckboxForList, $index);
        return $this->_rootElement->find($formatCheckbox, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * @param $index
     * Price for link on data list
     * @return string
     */
    public function getDownloadableLinksDataPriceForList($index)
    {
        $formatPrice = sprintf($this->downloadLinksDataPriceForList, $index);
        return $this->_rootElement->find($formatPrice, Locator::SELECTOR_XPATH)->getText();
    }
}
