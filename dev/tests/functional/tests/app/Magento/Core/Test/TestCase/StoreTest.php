<?php
/**
 * Store test
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\TestCase;

use Mtf\TestCase\Functional;
use Mtf\Factory\Factory;

class StoreTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * @ZephyrId MAGETWO-12405
     */
    public function testCreateNewLocalizedStoreView()
    {
        $storeFixture = Factory::getFixtureFactory()->getMagentoCoreStore();

        $storeListPage = Factory::getPageFactory()->getAdminSystemStore();
        $storeListPage->open();
        $storeListPage->getPageActionsBlock()->addStoreView();

        $newStorePage = Factory::getPageFactory()->getAdminSystemStoreNewStore();
        $newStorePage->getFormBlock()->fill($storeFixture);
        $newStorePage->getPageActionsBlock()->clickSave();
        $storeListPage->getMessagesBlock()->assertSuccessMessage();
        $this->assertContains(
            'The store view has been saved', $storeListPage->getMessagesBlock()->getSuccessMessages()
        );
        $this->assertTrue(
            $storeListPage->getGridBlock()->isStoreExists($storeFixture->getName())
        );

        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushCacheStorage();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

        $configPage = Factory::getPageFactory()->getAdminSystemConfig();
        $configPage->open();
        $storeSwitcher = $configPage->getStoreSwitcher();
        $storeSwitcher->selectStore(
            $storeFixture->getData('fields/group/value'), $storeFixture->getName()
        );
        $configGroup = $configPage->getForm()->getGroup('Locale Options');
        $configGroup->open();
        $configGroup->setValue('select-groups-locale-fields-code-value', 'German (Germany)');
        $configPage->getActions()->clickSave();
        $configPage->getMessagesBlock()->assertSuccessMessage();

        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $homePage->open();

        $homePage->getStoreSwitcherBlock()->selectStoreView($storeFixture->getName());
        $this->assertTrue($homePage->getSearchBlock()->isPlaceholderContains('Den gesamten Shop durchsuchen'));
    }
}
