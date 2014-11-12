<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Install\Test\Page\Install;
use Magento\User\Test\Fixture\User;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Mtf\ObjectManager;
use Magento\Install\Test\Constraint\AssertAgreementTextPresent;
use Magento\Install\Test\Constraint\AssertSuccessfulCheck;
use Magento\Install\Test\Constraint\AssertSuccessfulDbConnection;
use Magento\Install\Test\Constraint\AssertSuccessfulInstall;

/**
 * Test Flow:
 *
 * Preconditions
 * 1. Uninstall Magento.
 *
 * Steps
 * 1. Go setup landing page.
 * 2. Click on "Terms and agreements" button.
 * 3. Check license agreement text.
 * 4. Return back to landing page and click "Agree and Setup" button.
 * 5. Click "Start Readiness Check" button.
 * 6. Make sure PHP Version, PHP Extensions and File Permission are ok.
 * 7. Click "Next" and fill DB credentials.
 * 8. Click "Test Connection and Authentication" and make sure connection successful.
 * 9. Click "Next" and fill store address and admin path.
 * 10. Click "Next" and leave all default values.
 * 11. Click "Next" and fill admin user info.
 * 12. Click "Next" and on the "Step 6: Install" page click "Install Now" button.
 * 13. Perform assertions.
 *
 * @group Installer and Upgrade/Downgrade (PS)
 * @ZephyrId MAGETWO-15081
 */
class InstallTest extends Injectable
{
    /**
     * Uninstall Magento before test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $magentoBaseDir = dirname(dirname(dirname(MTF_BP)));
        $systemConfig = ObjectManager::getInstance()->create('Mtf\System\Config');
        // database
        $configData = $systemConfig->getConfigParam('install_data/db_credentials');
        //url
        $urlConfig = $systemConfig->getConfigParam('install_data/url');
        $configData['web'] = $urlConfig['base_url'];
        $configData['admin'] = $urlConfig['backend_frontname'];
        // admin user
        $adminCredentials = $systemConfig->getConfigParam('application/backend_user_credentials');
        $userData['username'] = $adminCredentials['login'];
        $userData['password'] = $adminCredentials['password'];
        $userData['password_confirmation'] = $adminCredentials['password'];
        $user = $fixtureFactory->createByCode('user', ['dataSet' => 'default', 'data' => $userData]);
        shell_exec("php -f $magentoBaseDir/setup/index.php uninstall");
        return ['configData' => $configData, 'user' => $user];
    }

    /**
     * @var Install
     */
    protected $installPage;

    /**
     * @var CmsIndex
     */
    protected $homePage;

    /**
     * Injection data.
     *
     * @param CmsIndex $homePage
     * @param Install $installPage
     * @return void
     */
    public function __inject(Install $installPage, CmsIndex $homePage)
    {
        $this->installPage = $installPage;
        $this->homePage = $homePage;
    }

    /**
     * Install Magento via web interface.
     *
     * @param array $configData
     * @param User $user
     * @param FixtureFactory $fixtureFactory
     * @param AssertAgreementTextPresent $assertLicense
     * @param AssertSuccessfulCheck $assertReadiness
     * @param AssertSuccessfulDbConnection $assertDbConnection
     * @param AssertSuccessfulInstall $assertInstall
     * @return void
     */
    public function test(
        array $configData,
        User $user,
        FixtureFactory $fixtureFactory,
        AssertAgreementTextPresent $assertLicense,
        AssertSuccessfulCheck $assertReadiness,
        AssertSuccessfulDbConnection $assertDbConnection,
        AssertSuccessfulInstall $assertInstall
    ) {
        // Preconditions
        $installConfig = $fixtureFactory->createByCode('install', ['data' => $configData]);
        // Steps
        $this->homePage->open();
        // Verify license agreement
        $this->installPage->getLandingBlock()->clickTermsAndAgreement();
        $assertLicense->processAssert($this->installPage);
        $this->installPage->getLicenseBlock()->clickBack();
        $this->installPage->getLandingBlock()->clickAgreeAndSetup();
        // Step 1: Readiness Check
        $this->installPage->getReadinessBlock()->clickReadinessCheck();
        $assertReadiness->processAssert($this->installPage);
        $this->installPage->getReadinessBlock()->clickNext();
        // Step 2: Add a Database
        $this->installPage->getDatabaseBlock()->fill($installConfig);
        $this->installPage->getDatabaseBlock()->clickTestConnection();
        $assertDbConnection->processAssert($this->installPage);
        $this->installPage->getDatabaseBlock()->clickNext();
        // Step 3: Web Configuration
        $this->installPage->getWebConfigBlock()->fill($installConfig);
        $this->installPage->getWebConfigBlock()->clickNext();
        // Step 4: Customize Your Store
        $this->installPage->getCustomizeStoreBlock()->clickNext();
        // Step 5: Create Admin Account
        $this->installPage->getCreateAdminBlock()->fill($user);
        $this->installPage->getCreateAdminBlock()->clickNext();
        // Step 6: Install
        $this->installPage->getInstallBlock()->clickInstallNow();
        $assertInstall->processAssert($this->installPage,$configData,$user);
        $this->installPage->getInstallBlock()->clickLaunchAdmin();
    }
}