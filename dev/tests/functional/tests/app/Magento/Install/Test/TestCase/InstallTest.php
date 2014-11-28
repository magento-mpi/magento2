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
use Magento\Install\Test\Fixture\Install as InstallConfig;
use Magento\User\Test\Fixture\User;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Mtf\ObjectManager;
use Magento\Install\Test\Constraint\AssertAgreementTextPresent;
use Magento\Install\Test\Constraint\AssertSuccessfulReadinessCheck;
use Magento\Install\Test\Constraint\AssertSuccessDbConnection;
use Magento\Install\Test\Constraint\AssertSuccessInstall;

/**
 * PLEASE ADD NECESSARY INFO BEFORE RUNNING TEST TO
 * ../dev/tests/functional/config/install_data.yml.dist
 *
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
     * Install page.
     *
     * @var Install
     */
    protected $installPage;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $homePage;

    /**
     * Uninstall Magento before test.
     *
     * @param FixtureFactory $fixtureFactory
     * @param ObjectManager $objectManager
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory, ObjectManager $objectManager)
    {
        $systemConfig = $objectManager->getInstance()->create('Mtf\System\Config');
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
        $installConfig = $fixtureFactory->createByCode('install', ['data' => $configData]);

        return ['user' => $user, 'installConfig' => $installConfig];
    }

    /**
     * Injection data.
     *
     * @param CmsIndex $homePage
     * @param Install $installPage
     * @return void
     */
    public function __inject(Install $installPage, CmsIndex $homePage)
    {
        $magentoBaseDir = dirname(dirname(dirname(MTF_BP)));
        // Uninstall Magento
        shell_exec("php -f $magentoBaseDir/setup/index.php uninstall");
        $this->installPage = $installPage;
        $this->homePage = $homePage;
    }

    /**
     * Install Magento via web interface.
     *
     * @param User $user
     * @param InstallConfig $installConfig
     * @param AssertAgreementTextPresent $assertLicense
     * @param AssertSuccessfulReadinessCheck $assertReadiness
     * @param AssertSuccessDbConnection $assertDbConnection
     * @param AssertSuccessInstall $assertInstall
     * @return void
     */
    public function test(
        User $user,
        InstallConfig $installConfig,
        AssertAgreementTextPresent $assertLicense,
        AssertSuccessfulReadinessCheck $assertReadiness,
        AssertSuccessDbConnection $assertDbConnection,
        AssertSuccessInstall $assertInstall
    ) {
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
        $assertInstall->processAssert($this->installPage, $installConfig, $user); //передавть инсталлКонфиг
        $this->installPage->getInstallBlock()->clickLaunchAdmin();
    }
}
