<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit;

use Mtf\Block\Block;
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
