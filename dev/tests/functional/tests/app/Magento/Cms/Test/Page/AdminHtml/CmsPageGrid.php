<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page\AdminHtml;

use Magento\Cms\Test\Block\AdminHtml\Page\Grid;
use Magento\Core\Test\Block\Messages;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CmsPageGrid
 * Cms Page backend grid page.
 *
 * @package Magento\Cms\Test\Page\AdminHtml
 */
class CmsPageGrid extends Page
{
    /**
     * URL for cms page
     */
    const MCA = 'admin/cms_page';

    /**
     *  Backend Cms Page grid id.
     *
     * @var Grid
     */
    protected $cmsPageGridBlock = '#cmsPageGrid';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Initialize page. Set page url
     *
     * @return void
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Getter for cms page grid block
     *
     * @return Grid
     */
    public function getCmsPageGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCmsAdminHtmlPageGrid(
            $this->_browser->find($this->cmsPageGridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Getter for global page message
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }
}

