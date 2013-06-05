<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Image_AdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Object Manager Helper
     *
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp() {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    /**
     * @dataProvider createDataProvider
     * @param string $adapter
     * @param string $class
     */
    public function testCreate($adapter, $class)
    {
        $imageAdapter = $this->getMock($class, array('checkDependencies'), array(), '', false);
        $imageAdapter->expects($this->any())
            ->method('checkDependencies')
            ->will($this->returnValue(null));
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock->expects($this->any())
           ->method('create')
           ->will($this->returnValue($imageAdapter));
        $this->_getModel(array(
            'objectManager' => $objectManagerMock,
        ))->create($adapter);

        $storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $storeConfigMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($adapter));

        $this->_getModel(array(
            'storeConfig' => $storeConfigMock,
            'objectManager' => $objectManagerMock,
        ))->create();
    }

    /**
     * @see self::testCreate()
     * @return array
     */
    public function createDataProvider()
    {
        return array(
            array(Mage_Core_Model_Image_AdapterFactory::ADAPTER_GD2, 'Varien_Image_Adapter_Gd2'),
            array(Mage_Core_Model_Image_AdapterFactory::ADAPTER_IM, 'Varien_Image_Adapter_ImageMagick'),
        );
    }

    /**
     * @covers Mage_Core_Model_Image_AdapterFactory::create
     * @dataProvider invalidArgumentExceptionDataProvider
     * @expectedException InvalidArgumentException
     * @param string $adapter
     */
    public function testInvalidArgumentException($adapter)
    {
        $this->_getModel()->create($adapter);
    }

    /**
     * @see self::testInvalidArgumentException()
     * @return array
     */
    public function invalidArgumentExceptionDataProvider()
    {
        return array(
            array(''),
            array('incorrect'),
        );
    }

    /**
     * @covers Mage_Core_Model_Image_AdapterFactory::create
     * @expectedException Mage_Core_Exception
     */
    public function testMageCoreException()
    {
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $imageAdapter = $this->getMockForAbstractClass('Varien_Image_Adapter_Abstract');
        $imageAdapter->expects($this->any())
            ->method('checkDependencies')
            ->will($this->throwException(new Exception));
        $objectManagerMock->expects($this->any())
           ->method('create')
           ->will($this->returnValue($imageAdapter));
        $this->_getModel(array(
            'objectManager' => $objectManagerMock,
        ))->create();
    }

    /**
     * @param array $mockObjects
     * @return Mage_Core_Model_Image_AdapterFactory
     */
    protected function _getModel(array $mockObjects = array())
    {
        return $this->_objectManagerHelper->getObject(
            'Mage_Core_Model_Image_AdapterFactory',
            $mockObjects
        );
    }
}
