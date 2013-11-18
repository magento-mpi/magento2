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
}
