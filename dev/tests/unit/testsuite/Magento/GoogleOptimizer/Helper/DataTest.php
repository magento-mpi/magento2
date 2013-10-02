<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 *
 */
namespace Magento\GoogleOptimizer\Helper;

/**
 * Class DataTest
 * @package Magento\GoogleOptimizer\Helper
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_googleAnalyticsHelperMock;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_storeConfigMock = $this->getMock('Magento\Core\Model\Store\ConfigInterface');
        $this->_googleAnalyticsHelperMock = $this->getMock('Magento\GoogleAnalytics\Helper\Data', array(), array(), '',
            false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $context = $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);
        $this->_helper = $objectManagerHelper->getObject('Magento\GoogleOptimizer\Helper\Data', array(
            'storeConfig' => $this->_storeConfigMock,
            'analyticsHelper' => $this->_googleAnalyticsHelperMock,
            'context' => $context
        ));
    }

    /**
     * @param bool $isExperimentsEnabled
     * @dataProvider dataProviderBoolValues
     */
    public function testGoogleExperimentIsEnabled($isExperimentsEnabled)
    {
        $store = 1;
        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with(\Magento\GoogleOptimizer\Helper\Data::XML_PATH_ENABLED, $store)
            ->will($this->returnValue($isExperimentsEnabled));

        $this->assertEquals($isExperimentsEnabled, $this->_helper->isGoogleExperimentEnabled($store));
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

    /**
     * @param bool $isExperimentsEnabled
     * @param bool $isAnalyticsAvailable
     * @param bool $result
     * @dataProvider dataProviderForTestGoogleExperimentIsActive
     */
    public function testGoogleExperimentIsActive($isExperimentsEnabled, $isAnalyticsAvailable, $result)
    {
        $store = 1;
        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with(\Magento\GoogleOptimizer\Helper\Data::XML_PATH_ENABLED, $store)
            ->will($this->returnValue($isExperimentsEnabled));

        $this->_googleAnalyticsHelperMock->expects($this->any())->method('isGoogleAnalyticsAvailable')
            ->with($store)
            ->will($this->returnValue($isAnalyticsAvailable));

        $this->assertEquals($result, $this->_helper->isGoogleExperimentActive($store));
    }

    /**
     * @return array
     */
    public function dataProviderForTestGoogleExperimentIsActive()
    {
        return array(
            array(true, true, true),
            array(false, true, false),
            array(false, false, false),
            array(true, false, false),
        );
    }
}
