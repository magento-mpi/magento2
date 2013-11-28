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
use Magento\Backend\Test\Block\HeaderPanel;

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
     * Header panel of admin dashboard
     *
     * @var AdminPanelHeader
     * @locator css:.header-panel
     * @injectable
     */
    protected $adminPanelHeader;

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->adminPanelHeader = Factory::getBlockFactory()->getMagentoBackendPageHeader(
            $this->_browser->find('header-panel', Locator::SELECTOR_CLASS_NAME));
    }

    /**
     * @return \Magento\Backend\Test\Block\HeaderPanel
     */
    public function getAdminPanelHeader()
    {
        return $this->adminPanelHeader;
    }
}
