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
     * @var Magento_Core_Model_Config_Section_ReaderPool
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Config_Section_Reader_DefaultReader
     */
    protected $_defaultReaderMock;

    /**
     * @var Magento_Core_Model_Config_Section_Reader_Website
     */
    protected $_websiteReaderMock;

    /**
     * @var Magento_Core_Model_Config_Section_Reader_Store
     */
    protected $_storeReaderMock;

    protected function setUp()
    {
        $this->_defaultReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Section_Reader_DefaultReader', array(), array(), '', false
        );
        $this->_websiteReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Section_Reader_Website', array(), array(), '', false
        );
        $this->_storeReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Section_Reader_Store', array(), array(), '', false
        );

        $this->_model = new Magento_Core_Model_Config_Section_ReaderPool(
            $this->_defaultReaderMock,
            $this->_websiteReaderMock,
            $this->_storeReaderMock
        );
    }

    /**
     * @covers Magento_Core_Model_Config_Section_ReaderPool::getReader
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
                'expectedResult' => 'Magento_Core_Model_Config_Section_Reader_DefaultReader'
            ),
            array(
                'scope' => 'website',
                'expectedResult' => 'Magento_Core_Model_Config_Section_Reader_Website'
            ),
            array(
                'scope' => 'websites',
                'expectedResult' => 'Magento_Core_Model_Config_Section_Reader_Website'
            ),
            array(
                'scope' => 'store',
                'expectedResult' => 'Magento_Core_Model_Config_Section_Reader_Store'
            ),
            array(
                'scope' => 'stores',
                'expectedResult' => 'Magento_Core_Model_Config_Section_Reader_Store'
            )
        );
    }
}
