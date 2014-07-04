<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Links
 * Page Top Links block
 */
class Links extends Block
{
    /**
     * Selector for qty products on compare
     *
     * @var string
     */
    protected $qtyCompareProducts = '//a[contains(text(), "Compare Products")]/span[contains(@class, "counter qty")]';

    /**
     * Link selector
     *
     * @var string
     */
    protected $link = '//a[contains(text(), "%s")]';

    /**
     * Open Link by title
     *
     * @param string $linkTitle
     * @return Element
     */
    public function openLink($linkTitle)
    {
        $this->_rootElement->find(sprintf($this->link, $linkTitle), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Is visible Link by title
     *
     * @param string $linkTitle
     * @return bool
     */
    public function isLinkVisible($linkTitle)
    {
        return $this->_rootElement->find(sprintf($this->link, $linkTitle), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Get count products adds to compare
     *
     * @return string
     */
    public function getQtyCompareProducts()
    {
        return trim(
            str_replace(
                ['items', 'item'],
                '',
                $this->_rootElement->find($this->qtyCompareProducts, Locator::SELECTOR_XPATH)->getText()
            )
        );
    }

    /**
     * Get url from link
     *
     * @param string $linkTitle
     * @return string
     */
    public function getLinkUrl($linkTitle)
    {
        return trim($this->_rootElement->find(sprintf($this->link, $linkTitle), Locator::SELECTOR_XPATH)->getUrl());
    }
}
