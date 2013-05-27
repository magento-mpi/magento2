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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_googleAnalyticsHelperMock;

    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_contentMock = $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_ConfigInterface');
        $this->_googleAnalyticsHelperMock = $this->getMock(
            'Mage_GoogleAnalytics_Helper_Data',
            array(),
            array(),
            '',
            false
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Helper_Data', array(
            'context' => $this->_contentMock,
            'storeConfig' => $this->_storeConfigMock,
            'analyticsHelper' => $this->_googleAnalyticsHelperMock,
        ));
    }

    /**
     * @dataProvider dataProviderBoolValues
     * @param $isExperiments
     * @param $isAnalytics
     */
    public function testGoogleExperimentIsActive($isExperiments, $isAnalytics, $result)
    {
        $store = 1;

        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with('google/analytics/experiments')
            ->will($this->returnValue($isExperiments));

        $this->_googleAnalyticsHelperMock->expects($this->any())->method('isGoogleAnalyticsAvailable')
            ->will($this->returnValue($isAnalytics));


        $this->assertEquals($result, $this->_helper->isGoogleExperimentActive($store));
    }

    /**
     * DataProvider for testGoogleExperimentIsActive
     * @return array
     */
    public function dataProviderBoolValues()
    {
        return array(
            array(true, true, true),
            array(false, true, false),
            array(false, false, false),
            array(true, false, false),
        );
    }
}
