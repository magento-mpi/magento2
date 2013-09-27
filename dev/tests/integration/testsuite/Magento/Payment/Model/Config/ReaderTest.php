<?php
/**
 * \Magento\Payment\Model\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Config\Reader
     */
    protected $_model;

    /** @var  \Magento\Config\FileResolverInterface/PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    public function setUp()
    {
        /** @var $cache \Magento\Core\Model\Cache */
        $cache = \Mage::getModel('Magento\Core\Model\Cache');
        $cache->clean();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = $objectManager->create('Magento\Payment\Model\Config\Reader',
            array('fileResolver'=>$this->_fileResolverMock));
    }

    public function testRead()
    {
        $fileList = array(__DIR__ . '/../_files/payment.xml');
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));
        $result = $this->_model->read('global');
        $expected = array(
            'credit_cards' => array(
                'SO' => 'Solo',
                'SM' => 'Switch/Maestro',
            ),
            'groups' => array(
                'paypal' => 'PayPal'
            ),
        );
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/../_files/payment.xml',
            __DIR__ . '/../_files/payment2.xml'
        );
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));

        $result = $this->_model->read('global');
        $expected = array(
            'credit_cards' => array(
                'AE' => 'American Express',
                'SM' => 'Switch/Maestro',
                'SO' => 'Solo',
            ),
            'groups' => array(
                'paypal' => 'PayPal Payment Methods',
                'offline' => 'Offline Payment Methods',
            ),
        );
        $this->assertEquals($expected, $result);
    }
}
