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
 * Class DownloadableSamples
 * for get samples blocks on frontend
 */
class DownloadableSamples extends Block
{
    /**
     * Title for for samples
     *
     * @var string
     */
    protected $downloadableSamplesDataTitleForForSample = '//dl[contains(@class,"downloadable samples")]/dt[contains(@class,"samples title")]';

    /**
     * Title item sample
     *
     * @var string
     */
    protected $downloadableSampleDataTitleForList = '//dl[contains(@class,"downloadable samples")]/dd[contains(@class,"sample item")][%d]/a';

    /**
     * Text for for samples data
     * @return string
     */
    public function getDownloadableSamplesDataTitleForForLink()
    {
        return $this->_rootElement->find(
            $this->downloadableSamplesDataTitleForForSample,
            Locator::SELECTOR_XPATH
        )->getText();
    }

    /**
     * @param $index
     * Text for sample on data list
     * @return string
     */
    public function getDownloadableSamplesDataTitleForList($index)
    {
        $formatTitle = sprintf($this->downloadableSampleDataTitleForList, $index);
        return $this->_rootElement->find($formatTitle, Locator::SELECTOR_XPATH)->getText();
    }
}
