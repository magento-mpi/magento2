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

/**
 * Class Dashboard
 * Dashboard (Home) page for backend
 *
 * @package Magento\Backend\Test\Page
 */
class Dashboard extends Page
{
    /**
     * URL part for backend authorization
     */
    const MCA = 'admin/dashboard';

    /**
     * Header panel block of dashboard page
     *
     * @var string
     */
    protected $adminPanelHeader = 'header-panel';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get admin panel header block instance
     *
     * @return \Magento\Backend\Test\Block\Page\Header
     */
    public function getAdminPanelHeader()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageHeader(
            $this->_browser->find($this->adminPanelHeader, Locator::SELECTOR_CLASS_NAME)
        );
    }
}
