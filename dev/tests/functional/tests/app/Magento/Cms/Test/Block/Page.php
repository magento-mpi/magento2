<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Cms Page block for the content on the frontend.
 */
class Page extends Block
{
    /**
     * Cms page content class
     *
     * @var string
     */
    protected $cmsPageContentClass = ".column.main";

    /**
     * Cms page title
     *
     * @var string
     */
    protected $cmsPageTitle = ".page-title";

    /**
     * Cms page text locator
     *
     * @var string
     */
    protected $textSelector = "//div[contains(.,'%s')]";

    /**
     * Get page content text
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->_rootElement->find($this->cmsPageContentClass)->getText();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_rootElement->find($this->cmsPageTitle)->getText();
    }

    /**
     * Wait for text is visible in the block.
     *
     * @param string $text
     * @return void
     */
    public function waitUntilTextIsVisible($text)
    {
        $text = sprintf($this->textSelector, $text);
        $browser = $this->browser;
        $this->_rootElement->waitUntil(
            function () use ($browser, $text) {
                $blockText = $browser->find($text, Locator::SELECTOR_XPATH);
                return $blockText->isVisible() == true ? false : null;
            }
        );
    }
}
