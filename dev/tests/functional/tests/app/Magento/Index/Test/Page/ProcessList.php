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

namespace Magento\Index\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class ProcessList
 * Index Management page
 *
 * @package Magento\Backend\Test\Page
 */
class ProcessList extends Page
{
    /**
     * URL part for index process page
     */
    const MCA = 'admin/process/list/';


    /**
     * Index management grid
     *
     * @var string
     */
    protected $indexProcessGrid = '#indexer_processes_grid';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .success-msg';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get index grid block
     *
     * @return \Magento\Index\Test\Block\Adminhtml\Process\Index
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoIndexAdminhtmlProcessIndex(
            $this->_browser->find($this->indexProcessGrid, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }
}
