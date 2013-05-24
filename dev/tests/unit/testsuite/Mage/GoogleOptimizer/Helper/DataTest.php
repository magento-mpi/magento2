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
     * @dataProvider dataProviderBoolValues
     * @param $isExperiments
     * @param $isAnalytics
     */
    public function testGoogleExperimentIsActive($isExperiments, $isAnalytics)
    {
        $store = 1;

        $this->_storeConfigMock->expects($this->atLeastOnce())->method('getConfigFlag')
            ->with($this->logicalOr('google/analytics/experiments', 'google/analytics/active'))
            ->will($this->onConsecutiveCalls($isAnalytics, $isExperiments));

        $this->assertEquals(($isExperiments && $isAnalytics), $this->_helper->isGoogleExperimentActive($store));
    }

    /**
     * DataProvider for testGoogleExperimentIsActive
     * @return array
     */
    public function dataProviderBoolValues()
    {
        return array(
            array(true, true),
            array(false, true),
            array(false, false),
            array(true, false),
        );
    }
}
