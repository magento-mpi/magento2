<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Id for wpppe method which was deleted
     */
    const WPPPE = 'wpppe';

    /**
     * Website Id
     */
    const WEBSITE = 1;

    /**
     * @var Store
     */
    protected $_model;

    protected function setUp()
    {
        $config = $this->getMock(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            ['getValue', 'isSetFlag'],
            [],
            '',
            false
        );
        $config->expects($this->exactly(5))->method('getValue')->will($this->returnValue(0));
        $request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            ['getParam', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName'],
            [],
            '',
            false
        );
        $request->expects($this->once())->method('getParam')->will($this->returnValue(self::WEBSITE));
        $context = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            ['getScopeConfig', 'getRequest'],
            [],
            '',
            false
        );
        $context->expects($this->once())->method('getScopeConfig')->will($this->returnValue($config));
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Store',
            ['context' => $context]
        );
    }

    public function testGetPaypalDisabledMethodsForPayflowDirect()
    {
        $disabledMethods = $this->_model->getPaypalDisabledMethods();
        $this->assertArrayNotHasKey(self::WPPPE, $disabledMethods);
    }
}
