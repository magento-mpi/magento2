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
    protected $storeConfigMock;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->storeConfigMock = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $this->helperMock = $this->getMock('Magento\Core\Helper\Data', array('isDevAllowed'), array(), '', false);
        $this->model = new Config(
            $this->storeConfigMock,
            $this->helperMock
        );
    }

    public function testIsActive()
    {
        $store = 'some store';
        $result = 'result';

        $this->storeConfigMock->expects(
            $this->once()
        )->method(
            'getConfigFlag'
        )->with(
            $this->equalTo('dev/translate_inline/active'),
            $this->equalTo($store)
        )->will(
            $this->returnValue($result)
        );

        $this->assertEquals($result, $this->model->isActive($store));
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
