<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Constraint;

use Magento\Install\Test\Page\Install;
use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Fixture\User;

/**
 * Class AssertSuccessfulInstall
 * Check that Magento successfully installed.
 */
class AssertSuccessfulInstall extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Magento successfully installed.
     *
     * @param array $configData
     * @param User $user
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage, array $configData, User $user)
    {
        \PHPUnit_Framework_Assert::assertContains(
            $user->getUsername(),
            $installPage->getInstallBlock()->getAdminInfo(),
            'No admin name'
        );
        \PHPUnit_Framework_Assert::assertContains(
            $user->getEmail(),
            $installPage->getInstallBlock()->getAdminInfo(),
            'No admin email'
        );
        \PHPUnit_Framework_Assert::assertContains(
            $configData['web'],
            $installPage->getInstallBlock()->getAdminInfo(),
            'No store address'
        );
        \PHPUnit_Framework_Assert::assertContains(
            $configData['admin'],
            $installPage->getInstallBlock()->getAdminInfo(),
            'No admin path'
        );
        \PHPUnit_Framework_Assert::assertContains(
            $configData['dbName'],
            $installPage->getInstallBlock()->getDbInfo(),
            'No DB name'
        );
        \PHPUnit_Framework_Assert::assertContains(
            $configData['dbUser'],
            $installPage->getInstallBlock()->getDbInfo(),
            'No DB username'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Install successfully finished";
    }
}
