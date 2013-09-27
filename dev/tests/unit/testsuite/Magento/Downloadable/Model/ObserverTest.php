<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Downloadable_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Event_Observer
     */
    protected $_observer;

    /**
     * @var Magento_Downloadable_Model_Observer
     */
    protected $_model;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_helperJsonEncode;

    protected function setUp()
    {
        $this->_helperJsonEncode = $this->getMockBuilder('Magento_Core_Helper_Data')
            ->setMethods(array('jsonEncode'))
            ->disableOriginalConstructor()
            ->getMock();
        $itemsFactory = $this->getMock('Magento_Downloadable_Model_Resource_Link_Purchased_Item_CollectionFactory',
            array(), array(), '', false
        );
        $this->_model = new Magento_Downloadable_Model_Observer(
            $this->_helperJsonEncode,
            $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false),
            $this->getMock('Magento_Downloadable_Model_Link_PurchasedFactory', array(), array(), '', false),
            $this->getMock('Magento_Catalog_Model_ProductFactory', array(), array(), '', false),
            $this->getMock('Magento_Downloadable_Model_Link_Purchased_ItemFactory', array(), array(), '', false),
            $this->getMock('Magento_Checkout_Model_Session', array(), array(), '', false),
            $itemsFactory
        );
    }

    protected function tearDown()
    {
        $this->_helperJsonEncode = null;
        $this->_model = null;
        $this->_observer = null;
    }

    public function testDuplicateProductNotDownloadable()
    {
        $currentProduct = $this->getMock('Magento_Catalog_Model_Product', array('getTypeId'), array(), '', false);

        $currentProduct->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE));
        $currentProduct->expects($this->never())
            ->method('getTypeInstance');

        $this->_setObserverExpectedMethods($currentProduct, new Magento_Object());

        $this->_model->duplicateProduct($this->_observer);
    }

    public function testDuplicateProductEmptyLinks()
    {
        $currentProduct = $this->getMock('Magento_Catalog_Model_Product',
            array('getTypeId', 'getTypeInstance'), array(), '', false);
        $currentProduct->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE));
        $newProduct = $this->getMock('Magento_Catalog_Model_Product',
            array('getTypeId', 'getTypeInstance'), array(), '', false);

        $typeInstance = $this->getMock('Magento_Downloadable_Model_Product_Type',
            array('getLinks', 'getSamples'), array(), '', false);
        $typeInstance->expects($this->once())
            ->method('getLinks')
            ->will($this->returnValue(array()));
        $typeInstance->expects($this->once())
            ->method('getSamples')
            ->will($this->returnValue(new Magento_Object()));

        $currentProduct->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstance));

        $this->_setObserverExpectedMethods($currentProduct, $newProduct);

        $this->assertNull($newProduct->getDownloadableData());
        $this->_model->duplicateProduct($this->_observer);
        $this->assertEmpty($newProduct->getDownloadableData());
    }

    public function testDuplicateProductTypeFile()
    {
        $currentProduct = $this->getMock('Magento_Catalog_Model_Product',
            array('getTypeId', 'getTypeInstance'), array(), '', false);
        $currentProduct->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE));

        $newProduct = $this->getMock('Magento_Catalog_Model_Product',
            array('getTypeId', 'getTypeInstance'), array(), '', false);

        $links = $this->_getLinks();

        $samples = $this->_getSamples();

        $getLinks = new Magento_Object($links);

        $getSamples = new Magento_Object($samples);

        $typeInstance = $this->getMock('Magento_Downloadable_Model_Product_Type',
            array('getLinks', 'getSamples'), array(), '', false);
        $typeInstance->expects($this->atLeastOnce())
            ->method('getLinks')
            ->will($this->returnValue(array($getLinks)));
        $typeInstance->expects($this->atLeastOnce())
            ->method('getSamples')
            ->will($this->returnValue(array($getSamples)));

        $currentProduct->expects($this->atLeastOnce())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstance));

        $this->_setObserverExpectedMethods($currentProduct, $newProduct);

        $callbackJsonEncode = function ($arg) {
            return json_encode($arg);
        };
        $this->_helperJsonEncode->expects($this->atLeastOnce())
            ->method('jsonEncode')
            ->will($this->returnCallback($callbackJsonEncode));

        $this->assertNull($newProduct->getDownloadableData($newProduct));
        $this->_model->duplicateProduct($this->_observer);

        $newDownloadableData = $newProduct->getDownloadableData();
        $fileData = json_decode($newDownloadableData['link'][0]['file'], true);

        $this->assertEquals($links['price'], $newDownloadableData['link'][0]['price']);
        $this->assertEquals($links['link_file'][0], $fileData[0]['file'][0]);
        $this->assertEquals($samples['title'], $newDownloadableData['sample'][0]['title']);
        $this->assertEquals(false, $newDownloadableData['link'][0]['is_delete']);
        $this->assertEquals($links['number_of_downloads'], $newDownloadableData['link'][0]['number_of_downloads']);
    }

    /**
     * Get downloadable data without is_delete flag
     *
     * @return array
     */
    protected function _getDownloadableData()
    {
        return array(
            'sample' => array(array('id' => 1, 'is_delete' => '')),
            'link' => array(array('id' => 2, 'is_delete' => ''))
        );
    }

    /**
     * Get downloadable data with set is_delete flag
     *
     * @return array
     */
    protected function _getDownloadableDataForDelete()
    {
        return array(
            'sample' => array(array('id' => 1, 'is_delete' => '1')),
            'link' => array(array('id' => 2, 'is_delete' => '1'))
        );
    }

    /**
     * Set products to observer
     *
     * @param array $currentProduct
     * @param array $newProduct
     */
    protected function _setObserverExpectedMethods($currentProduct, $newProduct)
    {
        $this->_observer = $this->getMock('Magento_Event_Observer',
            array('getCurrentProduct', 'getNewProduct'), array(), '', false);
        $this->_observer->expects($this->once())
            ->method('getCurrentProduct')
            ->will($this->returnValue($currentProduct));
        $this->_observer->expects($this->once())
            ->method('getNewProduct')
            ->will($this->returnValue($newProduct));
    }

    /**
     * Get Downloadable Link Data
     *
     * @return array
     */
    protected function _getLinks()
    {
        return array(
            'link_id' => '36',
            'product_id' => '141',
            'sort_order' => '0',
            'number_of_downloads' => '0',
            'is_shareable' => '2',
            'link_url' => null,
            'link_file' => array(array(
                'file'        => '/l/i/lighthouse_3.jpg',
                'name'        => 'lighthouse_3.jpg',
                'size'        => 56665,
                'status'      => 'new',
                )),
            'link_type' => 'file',
            'sample_url' => null,
            'sample_file' => array(array(
                'file'        => '/a/b/lighthouse_3.jpg',
                'name'        => 'lighthouse_3.jpg',
                'size'        => 56665,
                'status'      => 'new',
                )),
            'sample_type' => 'file',
            'title' =>'Link Title',
            'price' =>'15.00',
        );
    }

    /**
     * Get Downloadable Sample Data
     *
     * @return array
     */
    protected function _getSamples()
    {
        return array(
            'sample_id' => '42',
            'sample_url' => null,
            'sample_file' => array(array(
                'file'        => '/b/r/lighthouse_3.jpg',
                'name'        => 'lighthouse_3.jpg',
                'size'        => 56665,
                'status'      => 'new',
                )),
            'sample_type' => 'file',
            'sort_order' => '0',
            'title' => 'Sample Title',
        );
    }
}
