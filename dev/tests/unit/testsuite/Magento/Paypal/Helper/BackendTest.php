<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Helper;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelper;

    /**
     * @var \Magento\Backend\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendConfig;

    /**
     * @var \Magento\Paypal\Helper\Backend
     */
    protected $_helper;

    public function setUp()
    {
        $this->_request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->_coreHelper = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->_backendConfig = $this->getMock('Magento\Backend\Model\Config', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $objectManager->getObject(
            'Magento\Paypal\Helper\Backend',
            [
                'httpRequest' => $this->_request,
                'coreHelper' => $this->_coreHelper,
                'backendConfig' => $this->_backendConfig
            ]
        );
    }

    public function testGetConfigurationCountryCodeFromRequest()
    {
        $this->_configurationCountryCodePrepareRequest('US');
        $this->_configurationCountryCodeAssertResult('US');
    }

    /**
     * @param string|null $request
     * @dataProvider getConfigurationCountryCodeFromConfigDataProvider
     */
    public function testGetConfigurationCountryCodeFromConfig($request)
    {
        $this->_configurationCountryCodePrepareRequest($request);
        $this->_configurationCountryCodePrepareConfig('GB');
        $this->_configurationCountryCodeAssertResult('GB');
    }

    public function getConfigurationCountryCodeFromConfigDataProvider()
    {
        return [
            [null],
            ['not country code'],
        ];
    }

    /**
     * @param string|null $request
     * @param string|null|false $config
     * @param string|null $default
     * @dataProvider getConfigurationCountryCodeFromDefaultDataProvider
     */
    public function testGetConfigurationCountryCodeFromDefault($request, $config, $default)
    {
        $this->_configurationCountryCodePrepareRequest($request);
        $this->_configurationCountryCodePrepareConfig($config);
        $this->_coreHelper->expects($this->once())
            ->method('getDefaultCountry')
            ->will($this->returnValue($default));
        $this->_configurationCountryCodeAssertResult($default);
    }

    public function getConfigurationCountryCodeFromDefaultDataProvider()
    {
        return [
            [null, false, 'DE'],
            ['not country code', false, 'DE'],
            ['not country code', '', 'any final result']
        ];
    }

    /**
     * Prepare request for test
     *
     * @param string|null $request
     */
    private function _configurationCountryCodePrepareRequest($request)
    {
        $this->_request->expects($this->once())
            ->method('getParam')
            ->with(\Magento\Paypal\Model\Config\StructurePlugin::REQUEST_PARAM_COUNTRY)
            ->will($this->returnValue($request));
    }

    /**
     * Prepare backend config for test
     *
     * @param string|null|false $config
     */
    private function _configurationCountryCodePrepareConfig($config)
    {
        $this->_backendConfig->expects($this->once())
            ->method('getConfigDataValue')
            ->with(\Magento\Paypal\Block\Adminhtml\System\Config\Field\Country::FIELD_CONFIG_PATH)
            ->will($this->returnValue($config));
    }

    /**
     * Assert result of getConfigurationCountryCode method
     *
     * @param string $expected
     */
    private function _configurationCountryCodeAssertResult($expected)
    {
        $this->assertEquals($expected, $this->_helper->getConfigurationCountryCode());
    }
}
