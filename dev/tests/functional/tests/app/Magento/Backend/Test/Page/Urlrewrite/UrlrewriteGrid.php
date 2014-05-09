<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Urlrewrite;

use Mtf\Page\Page,
    Mtf\Factory\Factory;

/**
 * Class UrlrewriteGrid
 * Backend URL rewrite grid page
 *
 */
class UrlrewriteGrid extends Page
{
    /**
     * URL for URL rewrite grid
     */
    const MCA = 'admin/urlrewrite/index';

    /**
     * Page actions block UI ID
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-actions';

    /**
     * Messages block UI ID
     *
     * @var string
     */

    protected $messagesBlock = '.messages .messages';

    /**
     * Init page. Set page URL.
     */
    protected function _init()
    {
        parent::_init();
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Retrieve page actions block
     *
     * @return \Magento\Backend\Test\Block\Urlrewrite\Actions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendUrlrewriteActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }

    /**
     * Retrieve messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock)
        );
    }
}
