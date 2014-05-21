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
 * Class Samples
 *
 * Downloadable samples blocks on frontend
 */
class Samples extends Block
{
    /**
     * Title selector for samples block
     *
     * @var string
     */
    protected $titleForSampleBlock = '//dt[contains(@class,"samples title")]';

    /**
     * Title selector item sample
     *
     * @var string
     */
    protected $titleForList = '//dd[contains(@class,"sample item")][%d]/a';

    /**
     * Get title for Samples block
     *
     * @return string
     */
    public function getTitleForSampleBlock()
    {
        return $this->_rootElement->find(
            $this->titleForSampleBlock,
            Locator::SELECTOR_XPATH
        )->getText();
    }

    /**
     * Get title for item sample on data list
     *
     * @param int $index
     * @return string
     */
    public function getItemTitle($index)
    {
        $formatTitle = sprintf($this->titleForList, $index);
        return $this->_rootElement->find($formatTitle, Locator::SELECTOR_XPATH)->getText();
    }
}
