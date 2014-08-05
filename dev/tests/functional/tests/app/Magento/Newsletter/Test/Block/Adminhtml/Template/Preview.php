<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Block\Adminhtml\Template;

use Mtf\Block\Block;
use Mtf\Client\Browser;
use Mtf\Client\Element;
use Mtf\Block\BlockFactory;

/**
 * Class Preview
 * Newsletter template preview
 */
class Preview extends Block
{
    /**
     * Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * IFrame locator
     *
     * @var string
     */
    protected $iFrame = '#preview_iframe';

    /**
     * Constructor
     *
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Browser $browser)
    {
        $this->browser = $browser;
        parent::__construct($element, $blockFactory);
    }

    /**
     * Get page content text
     *
     * @return string
     */
    public function getPageContent()
    {
        $this->browser->switchToFrame(new Element\Locator($this->iFrame));
        return $this->_rootElement->getText();
    }
}
