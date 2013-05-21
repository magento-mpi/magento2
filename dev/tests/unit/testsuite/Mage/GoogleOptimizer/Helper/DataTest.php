<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contentMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_contentMock = $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_ConfigInterface');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Helper_Data', array(
            'context' => $this->_contentMock,
            'storeConfig' => $this->_storeConfigMock,
        ));
    }

    /**
     * @param bool $value
     * @dataProvider dataProviderBoolValues
     */
    public function testGoogleExperimentIsActive($value)
    {
        $store = 1;
        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with('google/analytics/experiments', $store)->will($this->returnValue($value));

        $this->assertEquals($value, $this->_helper->isGoogleExperimentActive($store));
    }

    /**
     * @return array
     */
    public function dataProviderBoolValues()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
