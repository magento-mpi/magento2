<?php
/**
 * Magento_Payment_Model_Config
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Payment_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Payment_Model_Config
     */
    protected $_model = null;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $cache Magento_Core_Model_Cache */
        $cache = $objectManager->create('Magento_Core_Model_Cache');
        $cache->clean();
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $fileList = array(
            __DIR__ . '/_files/payment.xml',
            __DIR__ . '/_files/payment2.xml',
        );
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));
        $reader = $objectManager->create('Magento_Payment_Model_Config_Reader',
            array('fileResolver'=>$fileResolverMock));
        $data = $objectManager->create('Magento_Payment_Model_Config_Data', array('reader'=> $reader));
        $this->_model = $objectManager->create('Magento_Payment_Model_Config', array('dataStorage'=>$data));
    }

    public function testGetCcTypes()
    {
        $expected = array(
            'AE' => 'American Express',
            'SM' => 'Switch/Maestro',
            'SO' => 'Solo',
        );
        $ccTypes = $this->_model->getCcTypes();
        $this->assertEquals($expected, $ccTypes);
    }

    public function testGetGroups()
    {
        $expected = array(
            'paypal' => 'PayPal Payment Methods',
            'offline' => 'Offline Payment Methods',
        );
        $groups = $this->_model->getGroups();
        $this->assertEquals($expected, $groups);
    }
}
