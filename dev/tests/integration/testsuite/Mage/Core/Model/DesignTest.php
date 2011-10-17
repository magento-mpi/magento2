<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Design();
    }

    public function testLoadChange()
    {
        $this->_model->loadChange(1);
        $this->assertNull($this->_model->getId());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/design_change.php
     */
    public function testChangeDesign()
    {
        $designPackage = new Mage_Core_Model_Design_Package('frontend', 'default', 'default', 'default');
        $storeId = Mage::app()->getAnyStoreView(); // fixture design_change
        $design = new Mage_Core_Model_Design;
        $design->loadChange($storeId)->changeDesign($designPackage);
        $this->assertEquals('default/modern/default', $designPackage->getDesignTheme());
    }

    public function testCRUD()
    {
        $this->_model->setData(
            array(
                'store_id'  => 1,
                'design'    => 'default/default/default',
                /* Note: in order to load a design change it should be active within the store's time zone */
                'date_from' => date('Y-m-d', strtotime('-1 day')),
                'date_to'   => date('Y-m-d', strtotime('+1 day')),
            )
        );
        $this->_model->save();
        $this->assertNotEmpty($this->_model->getId());

        try {
            $model =  new Mage_Core_Model_Design();
            $model->loadChange(1);
            $this->assertEquals($this->_model->getId(), $model->getId());

            /* Design change that intersects with existing ones should not be saved, so exception is expected */
            try {
                $model->setId(null);
                $model->save();
                $this->fail('A validation failure is expected.');
            } catch (Mage_Core_Exception $e) {}

            $this->_model->delete();
        } catch (Exception $e) {
            $this->_model->delete();
            throw $e;
        }

        $model =  new Mage_Core_Model_Design();
        $model->loadChange(1);
        $this->assertEmpty($model->getId());
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection()
            ->joinStore()
            ->addDateFilter();
        /**
         * @todo fix and add addStoreFilter method
         */
        $this->assertEmpty($collection->getItems());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/design_change.php
     */
    public function testChangeDesignCache()
    {
        $date = Varien_Date::now(true);
        $storeId = Mage::app()->getAnyStoreView(); // fixture design_change

        $cacheId = 'design_change_' . md5($storeId . $date);

        $design = new Mage_Core_Model_Design;
        $design->loadChange($storeId, $date);

        $cachedDesign = Mage::app()->loadCache($cacheId);
        $cachedDesign = unserialize($cachedDesign);

        $this->assertTrue(is_array($cachedDesign));
        $this->assertEquals($cachedDesign['design'], $design->getDesign());

        $design->setDesign('default/default/default')->save();

        $design = new Mage_Core_Model_Design;
        $design->loadChange($storeId, $date);

        $cachedDesign = Mage::app()->loadCache($cacheId);
        $cachedDesign = unserialize($cachedDesign);

        $this->assertTrue(is_array($cachedDesign));
        $this->assertEquals($cachedDesign['design'], $design->getDesign());
    }
}
