<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler\Ui;

use Mtf\Fixture;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class LogoutUser
 * Handler for ui backend user logout
 *
 * @package Magento\Backend\Test\Handler\Ui
 */
class LogoutUser extends Ui
{
    /**
     * Logout admin user
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $homePage = Factory::getPageFactory()->getAdminDashboard();
        $headerBlock = $homePage->getAdminPanelHeader();
        $headerBlock->logOut();
    }
}
