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
use Mtf\Client\Element\Locator;

/**
 * Class Preview
 * Newsletter template preview
 */
class Preview extends Block
{
    /**
     * IFrame locator
     *
     * @var string
     */
    protected $iFrame = '#preview_iframe';

    /**
     * Get page content text
     *
     * @return string
     */
    public function getPageContent()
    {
        $this->browser->switchToFrame(new Locator($this->iFrame));
        return $this->_rootElement->getText();
    }
}
