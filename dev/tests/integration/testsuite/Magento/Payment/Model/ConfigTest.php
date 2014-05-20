<?php
/**
 * \Magento\Payment\Model\Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $_model = null;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $cache \Magento\Framework\App\Cache */
        $cache = $objectManager->create('Magento\Framework\App\Cache');
        $cache->clean();
        $fileResolverMock = $this->getMockBuilder(
            'Magento\Framework\Config\FileResolverInterface'
        )->disableOriginalConstructor()->getMock();
        $fileList = array(
            file_get_contents(__DIR__ . '/_files/payment.xml'),
            file_get_contents(__DIR__ . '/_files/payment2.xml')
        );
        $fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($fileList));
        $reader = $objectManager->create(
            'Magento\Payment\Model\Config\Reader',
            array('fileResolver' => $fileResolverMock)
        );
        $data = $objectManager->create('Magento\Payment\Model\Config\Data', array('reader' => $reader));
        $this->_model = $objectManager->create('Magento\Payment\Model\Config', array('dataStorage' => $data));
    }

    public function testGetCcTypes()
    {
        $expected = array('AE' => 'American Express', 'SM' => 'Switch/Maestro', 'SO' => 'Solo');
        $ccTypes = $this->_model->getCcTypes();
        $this->assertEquals($expected, $ccTypes);
    }

    public function testGetGroups()
    {
        $expected = array('any_payment' => 'Any Payment Methods', 'offline' => 'Offline Payment Methods');
        $groups = $this->_model->getGroups();
        $this->assertEquals($expected, $groups);
    }
}
