<?php
/**
 * \Magento\Payment\Model\Config\Reader
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Payment\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Config\Reader
     */
    protected $_model;

    /** @var  \Magento\Framework\Config\FileResolverInterface/PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $cache \Magento\Framework\App\Cache */
        $cache = $objectManager->create('Magento\Framework\App\Cache');
        $cache->clean();
        $this->_fileResolverMock = $this->getMockBuilder(
            'Magento\Framework\Config\FileResolverInterface'
        )->disableOriginalConstructor()->getMock();
        $this->_model = $objectManager->create(
            'Magento\Payment\Model\Config\Reader',
            ['fileResolver' => $this->_fileResolverMock]
        );
    }

    public function testRead()
    {
        $fileList = [file_get_contents(__DIR__ . '/../_files/payment.xml')];
        $this->_fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($fileList));
        $result = $this->_model->read('global');
        $expected = [
            'credit_cards' => ['SO' => 'Solo', 'SM' => 'Switch/Maestro'],
            'groups' => ['any_payment' => 'Any Payment'],
            'methods' => ['checkmo' => ['allow_multiple_address' => 1, 'allow_multiple_with_3dsecure' => 1]],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = [
            file_get_contents(__DIR__ . '/../_files/payment.xml'),
            file_get_contents(__DIR__ . '/../_files/payment2.xml'),
        ];
        $this->_fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($fileList));

        $result = $this->_model->read('global');
        $expected = [
            'credit_cards' => ['AE' => 'American Express', 'SM' => 'Switch/Maestro', 'SO' => 'Solo'],
            'groups' => ['any_payment' => 'Any Payment Methods', 'offline' => 'Offline Payment Methods'],
            'methods' => [
                'checkmo' => ['allow_multiple_address' => 1, 'allow_multiple_with_3dsecure' => 1],
                'deny-method' => ['allow_multiple_address' => 0],
            ],
        ];
        $this->assertEquals($expected, $result);
    }
}
