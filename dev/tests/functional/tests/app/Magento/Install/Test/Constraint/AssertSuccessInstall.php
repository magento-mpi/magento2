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
 * Check that Magento successfully installed.
 */
class AssertSuccessInstall extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Admin info fields mapping.
     *
     * @var array
     */
    protected $adminFieldsList = [
        ['pageData' => 'username', 'fixture' => 'username'],
        ['pageData' => 'e-mail', 'fixture' => 'email'],
        ['pageData' => 'your_store_address', 'fixture' => 'web'],
        ['pageData' => 'magento_admin_address', 'fixture' => 'admin']
    ];

    /**
     * Database info fields mapping.
     *
     * @var array
     */
    protected $dbFieldsList = [
        ['pageData' => 'database_name', 'fixture' => 'dbName'],
        ['pageData' => 'username', 'fixture' => 'dbUser']
    ];

    /**
     * Assert that Magento successfully installed.
     *
     * @param $installConfig
     * @param User $user
     * @param Install $installPage
     * @return void
     */
    public function processAssert(Install $installPage, $installConfig, User $user)
    {
        $adminData = $installPage->getInstallBlock()->getAdminInfo();
        $dbData = $installPage->getInstallBlock()->getDbInfo();

        $allData = array_merge($user->getData(), $installConfig->getData());
        $allData['admin'] = $allData['web'] . $allData['admin'] . '/';

        foreach ($this->adminFieldsList as $field) {
            \PHPUnit_Framework_Assert::assertEquals(
                $allData[$field['fixture']],
                $adminData[$field['pageData']],
                'Wrong admin information is displayed.'
            );
        }
        foreach ($this->dbFieldsList as $field) {
            \PHPUnit_Framework_Assert::assertEquals(
                $allData[$field['fixture']],
                $dbData[$field['pageData']],
                'Wrong database information is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Install successfully finished.";
    }
}
