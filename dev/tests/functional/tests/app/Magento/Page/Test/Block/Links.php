<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Page Top Links block
 *
 * @package Magento\Page\Test\Block
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
