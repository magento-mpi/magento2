<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Model_Product_Attribute_Media_Api.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 * @magentoDataFixture productMediaFixture
 */
class Magento_Catalog_Model_Product_Attribute_Media_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Attribute_Media_Api
     */
    protected $_model;

    /**
     * @var string
     */
    protected static $_filesDir;

    /**
     * @var string
     */
    protected static $_mediaTmpDir;

    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
        $this->_model = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Media_Api');
    }

    public static function setUpBeforeClass()
    {
        self::$_filesDir = realpath(__DIR__ . '/../../../../_files');
        self::$_mediaTmpDir = Mage::getSingleton('Magento_Catalog_Model_Product_Media_Config')->getBaseTmpMediaPath();
        $ioFile = new Magento_Io_File();
        $ioFile->mkdir(self::$_mediaTmpDir . "/m/a", 0777, true);
        copy(self::$_filesDir . '/magento_image.jpg', self::$_mediaTmpDir . '/m/a/magento_image.jpg');
    }

    public static function tearDownAfterClass()
    {
        Magento_Io_File::rmdirRecursive(self::$_mediaTmpDir . "/m/a");
        /** @var $config Magento_Catalog_Model_Product_Media_Config */
        $config = Mage::getSingleton('Magento_Catalog_Model_Product_Media_Config');
        Magento_Io_File::rmdirRecursive($config->getBaseMediaPath());
    }

    public static function productMediaFixture()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        $product->setTierPrice(array());
        $product->setData('media_gallery', array('images' => array(array('file' => '/m/a/magento_image.jpg',),)));
        $product->save();
    }

    /**
     * @covers Magento_Catalog_Model_Product_Attribute_Media_Api::items
     * @covers Magento_Catalog_Model_Product_Attribute_Media_Api::info
     */
    public function testItemsAndInfo()
    {
        $items = $this->_model->items(1);
        $this->assertNotEmpty($items);
        $this->assertEquals(1, count($items));
        $item = current($items);
        $this->assertArrayHasKey('file', $item);
        $this->assertArrayHasKey('label', $item);;
        $this->assertArrayHasKey('url', $item);

        $info = $this->_model->info(1, $item['file']);
        $this->assertArrayHasKey('file', $info);
        $this->assertArrayHasKey('label', $info);;
        $this->assertArrayHasKey('url', $info);
        return $item['file'];
    }

    /**
     * @depends testItemsAndInfo
     */
    public function testCreate()
    {
        $data = array(
            'file' => array(
                'mime'      => 'image/jpeg',
                'content'   => base64_encode(file_get_contents(self::$_filesDir.'/magento_small_image.jpg'))
            )
        );
        $this->_model->create(1, $data);
        $items = $this->_model->items(1);
        $this->assertEquals(2, count($items));
    }

    public function createFaultDataProvider()
    {
        return array(
            array('floor' => 'ceiling'),
            array('file' => array('mime' => 'test')),
            array('file' => array('mime' => 'image/jpeg', 'content' => 'not valid'))
        );
    }

    /**
     * @dataProvider createFaultDataProvider
     * @expectedException Magento_Api_Exception
     */
    public function testCreateFault($data)
    {
        $this->_model->create(1, $data);
    }

    /**
     * @depends testItemsAndInfo
     */
    public function testUpdate($file)
    {
        $data = array(
            'file' => array(
                'mime'      => 'image/jpeg',
                'content'   => base64_encode(file_get_contents(self::$_filesDir.'/magento_small_image.jpg'))
            )
        );
        $this->assertTrue($this->_model->update(1, $file, $data));
    }

    /**
     * @depends testItemsAndInfo
     * @expectedException Magento_Api_Exception
     */
    public function testRemove($file)
    {
        $this->assertTrue($this->_model->remove(1, $file));
        $this->_model->info(1, $file);
    }

    public function testTypes()
    {
        $types = $this->_model->types(4);
        $this->assertNotEmpty($types);
        $this->assertInternalType('array', $types);
        $type = current($types);
        $this->assertArrayHasKey('code', $type);
        $this->assertArrayHasKey('scope', $type);
    }
}
