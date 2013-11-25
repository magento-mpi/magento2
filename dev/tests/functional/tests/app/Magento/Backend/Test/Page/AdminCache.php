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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class AdminCache
 * Cache Management page
 *
 * @package Magento\Backend\Test\Page
 */
class AdminCache extends Page
{
    /**
     * URL part for cache management page
     */
    const MCA = 'admin/cache/';

    /**
     * Cache actions block
     *
     * @var string
     */
    protected $actionsBlock = 'div.page-actions';

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
     * @return \Magento\Backend\Test\Block\Cache\Actions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendCacheActions(
            $this->_browser->find($this->actionsBlock, Locator::SELECTOR_CSS)
        );
    }
}
