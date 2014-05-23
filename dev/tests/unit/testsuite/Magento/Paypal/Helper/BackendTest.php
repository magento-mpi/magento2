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
                'request' => $this->_request,
                'coreHelper' => $this->_coreHelper,
                'backendConfig' => $this->_backendConfig
            ]
        );
    }

    /**
     * @param string $expected
     * @param string|null $request
     * @param string|null|false $config
     * @param string|null $default
     * @dataProvider getConfigurationCountryCodeDataProvider
     */
    public function testGetConfigurationCountryCode($expected, $request, $config, $default)
    {
        $this->_request->expects($this->once())
            ->method('getParam')
            ->with(\Magento\Paypal\Model\Config\StructurePlugin::REQUEST_PARAM_COUNTRY)
            ->will($this->returnValue($request));
        if (isset($config)) {
            $this->_backendConfig->expects($this->once())
                ->method('getConfigDataValue')
                ->with(\Magento\Paypal\Block\Adminhtml\System\Config\Field\Country::FIELD_CONFIG_PATH)
                ->will($this->returnValue($config));
        } else {
            $this->_backendConfig->expects($this->never())
                ->method('getConfigDataValue');
        }
        if (isset($default)) {
            $this->_coreHelper->expects($this->once())
                ->method('getDefaultCountry')
                ->will($this->returnValue($default));
        } else {
            $this->_coreHelper->expects($this->never())
                ->method('getDefaultCountry');
        }
        $this->assertEquals($expected, $this->_helper->getConfigurationCountryCode());
    }

    public function getConfigurationCountryCodeDataProvider()
    {
        return [
            ['US', 'US', null, null],
            ['GB', null, 'GB', null],
            ['GB', 'not country code', 'GB', null],
            ['DE', null, false, 'DE'],
            ['DE', 'not country code', false, 'DE'],
            ['any final result', 'not country code', '', 'any final result']
        ];
    }
}
