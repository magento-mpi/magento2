<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Block\Catalog\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class Links extends Block
{
    /**
     * Selector to find label of checkbox by link title
     *
     * @var string
     */
    protected $labelByTitleSelectorTemplate = '//*[text()="%s"]';

    /**
     * @param array $links
     */
    public function check($links)
    {
        foreach ($links as $link) {
            $xpath = sprintf($this->labelByTitleSelectorTemplate, $link['title']);
            $this->_rootElement->find($xpath, Locator::SELECTOR_XPATH)->click();
        }
    }
}
