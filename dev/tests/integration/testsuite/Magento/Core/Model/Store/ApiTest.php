<?php
/**
 * Core module API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/**
 * Magento store Api tests
 *
 * @magentoDataFixture Magento/Core/_files/store.php
 */
class Magento_Core_Model_Store_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Test store info.
     */
    public function testInfo()
    {
        $expectedStore = Mage::app()->getStore('fixturestore');
        $storeInfo = Magento_Test_Helper_Api::call($this, 'storeInfo', array(
            'storeId' => 'fixturestore',
        ));
        $expectedData= $expectedStore->getData();
        $this->assertEquals($expectedData, $storeInfo);
    }

    /**
     * Test stores list.
     */
    public function testList()
    {
        $actualStores = Magento_Test_Helper_Api::call($this, 'storeList');
        $expectedStores = Mage::app()->getStores();
        /** @var Magento_Core_Model_Store $expectedStore */
        foreach ($expectedStores as $expectedStore) {
            $expectedStoreFound = false;
            foreach ($actualStores as $actualStore) {
                if ($actualStore['store_id'] == $expectedStore->getId()) {
                    $this->assertEquals($expectedStore->getData(), $actualStore);
                    $expectedStoreFound = true;
                }
            }
            if (!$expectedStoreFound) {
                $this->fail(sprintf('Store "%s" was not found in API response.', $expectedStore->getFrontendName()));
            }
        }
    }
}
