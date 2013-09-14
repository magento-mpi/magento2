<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_ReaderPoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Section\ReaderPool
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Config\Section\Reader\DefaultReader
     */
    protected $_defaultReaderMock;

    /**
     * @var \Magento\Core\Model\Config\Section\Reader\Website
     */
    protected $_websiteReaderMock;

    /**
     * @var \Magento\Core\Model\Config\Section\Reader\Store
     */
    protected $_storeReaderMock;

    protected function setUp()
    {
        $this->_defaultReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Section\Reader\DefaultReader', array(), array(), '', false
        );
        $this->_websiteReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Section\Reader\Website', array(), array(), '', false
        );
        $this->_storeReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Section\Reader\Store', array(), array(), '', false
        );

        $this->_model = new \Magento\Core\Model\Config\Section\ReaderPool(
            $this->_defaultReaderMock,
            $this->_websiteReaderMock,
            $this->_storeReaderMock
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\Section\ReaderPool::getReader
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
                'expectedResult' => 'Magento\Core\Model\Config\Section\Reader\DefaultReader'
            ),
            array(
                'scope' => 'website',
                'expectedResult' => 'Magento\Core\Model\Config\Section\Reader\Website'
            ),
            array(
                'scope' => 'websites',
                'expectedResult' => 'Magento\Core\Model\Config\Section\Reader\Website'
            ),
            array(
                'scope' => 'store',
                'expectedResult' => 'Magento\Core\Model\Config\Section\Reader\Store'
            ),
            array(
                'scope' => 'stores',
                'expectedResult' => 'Magento\Core\Model\Config\Section\Reader\Store'
            )
        );
    }
}
