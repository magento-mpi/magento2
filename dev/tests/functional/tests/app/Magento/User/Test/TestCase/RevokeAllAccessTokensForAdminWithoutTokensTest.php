<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\User\Test\Page\Adminhtml\UserEdit;
use Magento\User\Test\Page\Adminhtml\UserIndex;

/**
 * Revoke all access tokens for admin without tokens.
 *
 * Test Flow:
 *
 * Steps:
 * 1. Open Backend
 * 2. Open System > All Users
 * 3. Open admin from the user grid
 * 4. Click button Force Sign-in
 * 5. Click Ok on popup window
 * 6. Perform all asserts
 *
 * @group Web_API_Framework_(PS)
 * @ZephyrId MAGETWO-29675
 */
class RevokeAllAccessTokensForAdminWithoutTokensTest extends Injectable
{
    /**
     * UserIndex page.
     *
     * @var UserIndex
     */
    protected $userIndex;

    /**
     * UserEdit page.
     *
     * @var UserEdit
     */
    protected $userEdit;

    /**
     * Setup necessary data for test.
     *
     * @param UserIndex $userIndex
     * @param UserEdit $userEdit
     * @return void
     */
    public function __inject(
        UserIndex $userIndex,
        UserEdit $userEdit
    ) {
        $this->userIndex = $userIndex;
        $this->userEdit = $userEdit;
    }

    /**
     * Run Revoke all access tokens for admin without tokens test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function test(FixtureFactory $fixtureFactory)
    {
        /** @var \Magento\User\Test\Fixture\User $user */
        $user = $fixtureFactory->createByCode('user', ['dataSet' => 'default']);
        // Steps:
        $this->userIndex->open()->getUserGrid()->searchAndOpen(['username' => $user->getUsername()]);
        $this->userEdit->getPageActions()->forceSignIn();
    }
}
