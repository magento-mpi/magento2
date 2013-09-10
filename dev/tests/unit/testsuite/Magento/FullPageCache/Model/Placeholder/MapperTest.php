<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_MapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Placeholder_Mapper
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento_FullPageCache_Model_Container_PlaceholderFactory',
            array(), array(), '', false
        );
        $this->_configMock = $this->getMock('Magento_FullPageCache_Model_Placeholder_ConfigInterface');
        $this->_model = new Magento_FullPageCache_Model_Placeholder_Mapper($this->_factoryMock, $this->_configMock);
    }

    public function testMap()
    {
        $blockMock = $this->getMockForAbstractClass('Magento_Core_Block_Abstract', array(), '', false, false, true);
        $this->_model->map($blockMock);
    }
}
