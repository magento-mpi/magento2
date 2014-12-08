<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler\Ui;

use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui;

/**
 * Class LogoutUser
 * Handler for ui backend user logout
 *
 */
class LogoutUser extends Ui
{
    /**
     * Logout admin user
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $homePage = Factory::getPageFactory()->getAdminDashboard();
        $headerBlock = $homePage->getAdminPanelHeader();
        $headerBlock->logOut();
    }
}
