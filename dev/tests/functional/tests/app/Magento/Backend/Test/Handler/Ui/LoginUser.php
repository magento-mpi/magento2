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
 * Class LoginUser
 * Handler for ui backend user login
 *
 * @package Magento\Backend\Test\Handler\Ui
 */
class LoginUser extends Ui
{
    /**
     * Login admin user
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        if (null === $fixture) {
            $fixture = Factory::getFixtureFactory()->getMagentoBackendAdminSuperAdmin();
        }

        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        $loginForm = $loginPage->getLoginBlock();

        $loginPage->open();

        $adminHeaderPanel = $loginPage->getHeaderBlock();
        if (!$adminHeaderPanel || !$adminHeaderPanel->isVisible()) {
            $loginForm->fill($fixture);
            $loginForm->submit();
            $loginPage->waitForHeaderBlock();
        }
    }
}
