<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Scope;

class ReaderPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Scope\ReaderPool
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Config\Scope\Reader\DefaultReader
     */
    protected $_defaultReaderMock;

    /**
     * @var \Magento\Core\Model\Config\Scope\Reader\Website
     */
    protected $_websiteReaderMock;

    /**
     * @var \Magento\Core\Model\Config\Scope\Reader\Store
     */
    protected $_storeReaderMock;

    protected function setUp()
    {
        $this->_defaultReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Scope\Reader\DefaultReader', array(), array(), '', false
        );
        $this->_websiteReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Scope\Reader\Website', array(), array(), '', false
        );
        $this->_storeReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Scope\Reader\Store', array(), array(), '', false
        );

        $this->_model = new \Magento\Core\Model\Config\Scope\ReaderPool(
            $this->_defaultReaderMock,
            $this->_websiteReaderMock,
            $this->_storeReaderMock
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\Scope\ReaderPool::getReader
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
                'expectedResult' => 'Magento\Core\Model\Config\Scope\Reader\DefaultReader'
            ),
            array(
                'scope' => 'website',
                'expectedResult' => 'Magento\Core\Model\Config\Scope\Reader\Website'
            ),
            array(
                'scope' => 'websites',
                'expectedResult' => 'Magento\Core\Model\Config\Scope\Reader\Website'
            ),
            array(
                'scope' => 'store',
                'expectedResult' => 'Magento\Core\Model\Config\Scope\Reader\Store'
            ),
            array(
                'scope' => 'stores',
                'expectedResult' => 'Magento\Core\Model\Config\Scope\Reader\Store'
            )
        );
    }
}
