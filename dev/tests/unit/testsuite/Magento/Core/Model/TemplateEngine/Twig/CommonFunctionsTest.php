<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Twig;

class CommonFunctionsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\TemplateEngine\Twig\CommonFunctions */
    protected $_commonFunctions;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_urlBuilderMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_urlHelperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_dataHelperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_storeManagerMock;

    /** @var \Magento\Core\Model\View\Url  */
    protected $_viewUrl;

    /** @var \Magento\View\ConfigInterface   */
    protected $_viewConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_helperImageMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_loggerMock;

    /** @var \Magento\Core\Model\LocaleInterface  */
    protected $_localeMock;

    protected function setUp()
    {
        $this->_urlBuilderMock = $this->getMock('Magento\UrlInterface');
        $this->_urlHelperMock = $this->getMockBuilder('Magento\Core\Helper\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_dataHelperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_viewUrl = $this->getMockBuilder('Magento\Core\Model\View\Url')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_viewConfig = $this->getMockBuilder('Magento\View\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperImageMock = $this->getMockBuilder('Magento\Catalog\Helper\Image')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_loggerMock = $this->getMockBuilder('Magento\Core\Model\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_commonFunctions = new \Magento\Core\Model\TemplateEngine\Twig\CommonFunctions(
            $this->_urlBuilderMock,
            $this->_urlHelperMock,
            $this->_dataHelperMock,
            $this->_storeManagerMock,
            $this->_viewUrl,
            $this->_viewConfig,
            $this->_helperImageMock,
            $this->_loggerMock,
            $this->_localeMock
        );
    }

    /**
     * Test that the getFunctions return arrays of appropriate types
     */
    public function testGetFunctions()
    {
        /** @var array $functions */
        $functions = $this->_commonFunctions->getFunctions();

        $this->assertInternalType('array', $functions);
        $this->assertTrue(count($functions) >= 1, 'Functions array does not contain any elements');
        $this->assertContainsOnly('Twig_SimpleFunction', $functions, false,
            'Contains something that is not a Twig function.');

    }

    /**
     * Test getViewFileUrl happy path.
     */
    public function testGetViewFileUrl()
    {
        $themesUrl = "http://www.example.com/themes";

        $this->_viewUrl->expects($this->once())
            ->method('getViewFileUrl')
            ->will($this->returnValue($themesUrl));

        $actual = $this->_commonFunctions->getViewFileUrl('file');
        $this->assertEquals($themesUrl, $actual, 'Themes url returned from getViewFileUrl did not match expected');
    }

    /**
     * Test getViewFileUrl when model throws an exception
     */
    public function testGetViewFileUrlException()
    {
        $magentoException = new \Magento\Exception('test exception');
        $notFoundUrl = 'not found';

        $this->_viewUrl->expects($this->once())
            ->method('getViewFileUrl')
            ->will($this->throwException($magentoException));
        $this->_loggerMock->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($magentoException));
        $this->_urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($notFoundUrl));

        $actual = $this->_commonFunctions->getViewFileUrl('file');
        $this->assertEquals($notFoundUrl, $actual, 'Not Found url returned from getViewFileUrl did not match');
    }

    /**
     * Data provider for testGetSelectFromToHtml.
     *
     * Provide varying ranges of 'from' and 'to' indexes along with the
     * expected output array.
     *
     * @return array
     */
    public function getSelectFromToHtmlDataProvider()
    {
        return array(
            array( 1, 2, array(
                array('value' => '', 'label' => '-'),
                array('value' => '1', 'label' => '01'),
                array('value' => '2', 'label' => '02'))
            ),
            array( 8, 11, array(
                array('value' => '', 'label' => '-'),
                array('value' => '8', 'label' => '08'),
                array('value' => '9', 'label' => '09'),
                array('value' => '10', 'label' => '10'),
                array('value' => '11', 'label' => '11'))
            ),
            array( 99, 101, array(
                array('value' => '', 'label' => '-'),
                array('value' => '99', 'label' => '99'),
                array('value' => '100', 'label' => '100'),
                array('value' => '101', 'label' => '101'))
            )
        );
    }

    /**
     * @dataProvider getSelectFromToHtmlDataProvider
     */
    public function testGetSelectFromToHtml($fromNumber, $toNumber, $expectedOptions)
    {
        $selectBlockMock = $this->getMockBuilder('Magento\Core\Block\Html\Select')
            ->disableOriginalConstructor()
            ->getMock();;

        $selectBlockMock->expects($this->once())
            ->method('setOptions')
            ->with($this->equalTo($expectedOptions))
            ->will($this->returnValue($selectBlockMock));

        $name = 'name';
        $nameOptionsById = array('aliasA' => 'blockA');
        $optionsId = 'options_id';
        $this->_commonFunctions->getSelectFromToHtml($selectBlockMock,
            $name, $fromNumber, $toNumber, $nameOptionsById, $optionsId );
    }

    public function testGetSelectHtml()
    {
        $selectBlockMock = $this->getMockBuilder('Magento\Core\Block\Html\Select')
            ->disableOriginalConstructor()
            ->getMock();;

        $selectBlockMock->expects($this->once())
            ->method('setOptions')
            ->will($this->returnValue($selectBlockMock));

        $optionId = 'anId';
        $name = 'name';
        $nameOptionsById = array('aliasA' => 'blockA');;
        $this->_commonFunctions->getSelectHtml($selectBlockMock, $optionId, $name, $nameOptionsById );

    }
}
