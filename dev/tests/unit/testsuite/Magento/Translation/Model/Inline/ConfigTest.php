<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translation\Model\Inline;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $model;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->scopeConfigMock = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $this->helperMock = $this->getMock('Magento\Core\Helper\Data', array('isDevAllowed'), array(), '', false);
        $this->model = new Config(
            $this->scopeConfigMock,
            $this->helperMock
        );
    }

    public function testIsActive()
    {
        $store = 'some store';
        $result = 'result';
        $scopeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $scopeConfig->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            $this->equalTo('dev/translate_inline/active'),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->equalTo($store)
        )->will(
            $this->returnValue($result)
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $config = $objectManager->getObject(
            '\Magento\Translation\Model\Inline\Config',
            array('scopeConfig' => $scopeConfig)
        );
        $this->assertEquals($result, $config->isActive($store));
    }

    public function testIsDevAllowed()
    {
        $store = 'some store';
        $result = 'result';

        $this->helperMock->expects(
            $this->once()
        )->method(
            'isDevAllowed'
        )->with(
            $store
        )->will(
            $this->returnValue($result)
        );

        $this->assertEquals($result, $this->model->isDevAllowed($store));
    }
}
