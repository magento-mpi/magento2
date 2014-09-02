<?php
/**
 * Store test
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\TestCase;

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
        $objectManager = Factory::getObjectManager();
        $storeFixture = $objectManager->create('\Magento\Store\Test\Fixture\Store', ['dataSet' => 'german']);

        $storeListPage = Factory::getPageFactory()->getAdminSystemStore();
        $storeListPage->open();
        $storeListPage->getGridPageActions()->addStoreView();

        $newStorePage = Factory::getPageFactory()->getAdminSystemStoreNewStore();
        $newStorePage->getStoreForm()->fill($storeFixture);
        $newStorePage->getFormPageActions()->save();
        $storeListPage->getMessagesBlock()->assertSuccessMessage();
        $this->assertContains(
            'The store view has been saved',
            $storeListPage->getMessagesBlock()->getSuccessMessages()
        );
        $this->assertTrue(
            $storeListPage->getStoreGrid()->isStoreExists($storeFixture->getName())
        );

        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushCacheStorage();
        $cachePage->getMessagesBlock()->assertSuccessMessage();

        $configPage = Factory::getPageFactory()->getAdminSystemConfig();
        $configPage->open();
        $configPage->getPageActions()->selectStore($storeFixture->getGroupId() . "/" . $storeFixture->getName());
        $configGroup = $configPage->getForm()->getGroup('Locale Options');
        $configGroup->open();
        $configGroup->setValue('select-groups-locale-fields-code-value', 'German (Germany)');
        $configPage->getPageActions()->save();
        $configPage->getMessagesBlock()->assertSuccessMessage();

        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $homePage->open();

        $homePage->getStoreSwitcherBlock()->selectStoreView($storeFixture->getName());
        $this->assertTrue($homePage->getSearchBlock()->isPlaceholderContains('Den gesamten Shop durchsuchen'));
    }
}
