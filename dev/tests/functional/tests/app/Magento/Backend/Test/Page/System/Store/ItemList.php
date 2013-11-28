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
    const MCA = 'admin/system_store';

    /**
     * @var Messages
     */
    protected $_messagesBlock;

    /**
     * @var Actions
     */
    protected $_pageActionsBlock;

    /**
     * @var Grid
     */
    protected $_gridBlock;

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $blockFactory = Factory::getBlockFactory();
        $this->_messagesBlock = $blockFactory->getMagentoCoreMessages($this->_browser->find('#messages .messages'));
        $this->_gridBlock = $blockFactory->getMagentoBackendSystemStoreGrid($this->_browser->find('#storeGrid'));
        $this->_pageActionsBlock = $blockFactory->getMagentoBackendSystemStoreActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Retrieve  actions block
     *
     * @return Actions
     */
    public function getPageActionsBlock()
    {
        return $this->_pageActionsBlock;
    }

    /**
     * Retrieve store grid block
     *
     * @return Grid
     */
    public function getGridBlock()
    {
        return $this->_gridBlock;
    }

    /**
     * Retrieve messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return $this->_messagesBlock;
    }
}