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

namespace Magento\Backend\Test\Page;

use Mtf\Factory\Factory;
use Mtf\Page\Page;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Cache\Actions;

/**
 * Class Cache
 * Cache Management page
 *
 * @package Magento\Backend\Test\Page
 */
class Cache extends Page
{
    /**
     * URL part for cache management page
     */
    const MCA = 'admin/cache/';

    /**
     * Actions selector
     *
     * @var string
     */
    protected $actionsSelector = 'div.page-actions';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get the actions block
     *
     * @return Actions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendCacheActions($this->_browser->find($this->actionsSelector));
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
