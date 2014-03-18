<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Config\Reader;

class ReaderPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\Config\Reader\ReaderPool
     */
    protected $_model;

    /**
     * @var \Magento\Store\Model\Config\Reader\DefaultReader
     */
    protected $_defaultReaderMock;

    /**
     * @var \Magento\Store\Model\Config\Reader\Website
     */
    protected $_websiteReaderMock;

    /**
     * @var \Magento\Store\Model\Config\Reader\Store
     */
    protected $_storeReaderMock;

    protected function setUp()
    {
        $this->_defaultReaderMock = $this->getMock(
            'Magento\Store\Model\Config\Reader\DefaultReader', array(), array(), '', false
        );
        $this->_websiteReaderMock = $this->getMock(
            'Magento\Store\Model\Config\Reader\Website', array(), array(), '', false
        );
        $this->_storeReaderMock = $this->getMock(
            'Magento\Store\Model\Config\Reader\Store', array(), array(), '', false
        );

        $this->_model = new \Magento\Store\Model\Config\Reader\ReaderPool(
            $this->_defaultReaderMock,
            $this->_websiteReaderMock,
            $this->_storeReaderMock
        );
    }

    /**
     * @covers \Magento\Store\Model\Config\Reader\ReaderPool::getReader
     * @dataProvider getReaderDataProvider
     * @param string $scope
     * @param string $instanceType
     */
    public function testGetReader($scope, $instanceType)
    {
        $this->assertInstanceOf($instanceType, $this->_model->getReader($scope));
    }

    /**
     * @return array
     */
    public function getReaderDataProvider()
    {
        return array(
            array(
                'scope' => 'default',
                'expectedResult' => 'Magento\Store\Model\Config\Reader\DefaultReader'
            ),
            array(
                'scope' => 'website',
                'expectedResult' => 'Magento\Store\Model\Config\Reader\Website'
            ),
            array(
                'scope' => 'websites',
                'expectedResult' => 'Magento\Store\Model\Config\Reader\Website'
            ),
            array(
                'scope' => 'store',
                'expectedResult' => 'Magento\Store\Model\Config\Reader\Store'
            ),
            array(
                'scope' => 'stores',
                'expectedResult' => 'Magento\Store\Model\Config\Reader\Store'
            )
        );
    }
}
