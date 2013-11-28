<?php
/**
 * Store list page
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Page\System\Store;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use \Magento\Core\Test\Block\Messages;
use \Magento\Backend\Test\Block\System\Store\Actions;
use \Magento\Backend\Test\Block\System\Store\Grid;

class ItemList extends Page
{
    /**
     * Url
     */
    const MCA = 'admin/system_store';

    /**
     * Messages block selector
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Actions block selector
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-actions';

    /**
     * Store grid selector
     *
     * @var string
     */
    protected $gridBlock = '#storeGrid';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Retrieve  actions block
     *
     * @return Actions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendSystemStoreActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }

    /**
     * Retrieve store grid block
     *
     * @return Grid
     */
    public function getGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendSystemStoreGrid($this->_browser->find($this->gridBlock));
    }

    /**
     * Retrieve messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messagesBlock));
    }
}
