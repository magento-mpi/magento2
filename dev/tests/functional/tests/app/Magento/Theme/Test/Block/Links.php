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
 * Page Top Links block
 *
 */
class Links extends Block
{
    /**
     * Open Link by title
     *
     * @param string $linkTitle
     * @return Element
     */
    public function openLink($linkTitle)
    {
        $this->_rootElement
            ->find('//a[contains(text(), "' . $linkTitle . '")]', Locator::SELECTOR_XPATH)
            ->click();
    }
}
