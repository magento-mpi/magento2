<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Handler\Ui;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class LoginUser
 * Handler for ui backend user login
 *
 */
class LoginUser extends Ui
{
    /**
     * Login admin user
     *
     * @param FixtureInterface $fixture [optional]
     * @return void|mixed
     */
    public function persist(FixtureInterface $fixture = null)
    {
        if (null === $fixture) {
            $fixture = Factory::getFixtureFactory()->getMagentoBackendAdminSuperAdmin();
        }

        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        $loginForm = $loginPage->getLoginBlock();

        $adminHeaderPanel = $loginPage->getHeaderBlock();
        if (!$adminHeaderPanel || !$adminHeaderPanel->isVisible()) {
            $loginPage->open();
            if ($adminHeaderPanel->isVisible()) {
                return;
            }
            $loginForm->waitForElementVisible('input#username');
            $loginForm->fill($fixture);
            $loginForm->submit();
            $loginPage->waitForHeaderBlock();
        }
    }
}
