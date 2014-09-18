<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit;

use Mtf\Block\Block;
use Mtf\Block\BlockFactory;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Preview
 * Preview block for the preview page
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
     * Frame content selector
     *
     * @var string
     */
    protected $contentSelector = '.column.main';

    /**
     * Browser object
     *
     * @var Browser $browser
     */
    protected $browser;

    /**
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param array $config
     * @param Browser $browser
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Browser $browser, array $config = [])
    {
        parent::__construct($element, $blockFactory, $config);
        $this->browser = $browser;
    }

    /**
     * Returns page content
     *
     * @return string
     */
    public function getPageContent()
    {
        $this->browser->selectWindow();
        $this->browser->switchToFrame(new Locator($this->iFrame));
        $content = $this->browser->find($this->contentSelector)->getText();
        $this->browser->closeWindow();
        return $content;
    }
}
