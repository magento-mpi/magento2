<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Block\Adminhtml\Template;

class PreviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    const MALICIOUS_TEXT = 'test malicious';

    /**
     * Init data
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

    }

    /**
     * Check of processing email templates
     *
     * @param array $requestParamMap
     *
     * @dataProvider toHtmlDataProvider
     * @param $requestParamMap
     */
    public function testToHtml($requestParamMap)
    {
        $template = $this->getMock('Magento\Email\Model\Template',
            array('setDesignConfig', 'getDesignConfig', '__wakeup', 'getProcessedTemplate'), array(), '', false);
        $template->expects($this->once())
            ->method('getProcessedTemplate')
            ->with($this->equalTo(array()), $this->equalTo(true))
            ->will($this->returnValue(self::MALICIOUS_TEXT));
        $emailFactory = $this->getMock('Magento\Email\Model\TemplateFactory', array('create'), array(), '', false);
        $emailFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array('data' => array('area' => \Magento\Framework\App\Area::AREA_FRONTEND))))
            ->will($this->returnValue($template));


        $request = $this->getMock('Magento\Framework\App\RequestInterface');
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap($requestParamMap));
        $eventManage = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $design = $this->getMock('Magento\Framework\View\DesignInterface');
        $store = $this->getMock('Magento\Store\Model\Store', array('getId', '__wakeup'), array(), '', false);
        $store->expects($this->any())->method('getId')->will($this->returnValue(1));
        $storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getDefaultStoreView')->will($this->returnValue(null));
        $storeManager->expects($this->any())->method('getStores')->will($this->returnValue([$store]));

        $context = $this->getMock('Magento\Backend\Block\Template\Context',
            array('getRequest', 'getEventManager', 'getScopeConfig', 'getDesignPackage', 'getStoreManager'),
            array(), '', false
        );
        $context->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $context->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManage));
        $context->expects($this->any())->method('getScopeConfig')->will($this->returnValue($scopeConfig));
        $context->expects($this->any())->method('getDesignPackage')->will($this->returnValue($design));
        $context->expects($this->any())->method('getStoreManager')->will($this->returnValue($storeManager));

        $maliciousCode = $this->getMock(
            'Magento\Framework\Filter\Input\MaliciousCode',
            array('filter'),
            array(),
            '',
            false
        );
        $maliciousCode->expects($this->once())->method('filter')->with($this->equalTo($requestParamMap[1][2]))
            ->will($this->returnValue(self::MALICIOUS_TEXT));

        $preview = $this->objectManagerHelper->getObject(
            'Magento\Email\Block\Adminhtml\Template\Preview',
            array(
                'context' => $context,
                'emailFactory' => $emailFactory,
                'maliciousCode' => $maliciousCode
            )
        );
        $this->assertEquals(self::MALICIOUS_TEXT, $preview->toHtml());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function toHtmlDataProvider()
    {
        return array(
            array('data 1' => array(
                array('type', null, ''),
                array('text', null, sprintf('<javascript>%s</javascript>', self::MALICIOUS_TEXT)),
                array('styles', null, '')
            )),
            array('data 2' => array(
                array('type', null, ''),
                array('text', null, sprintf('<iframe>%s</iframe>', self::MALICIOUS_TEXT)),
                array('styles', null, '')
            )),
            array('data 3' => array(
                array('type', null, ''),
                array('text', null, self::MALICIOUS_TEXT),
                array('styles', null, '')
            )),
        );
    }
}
