<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate;

class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\ScopeResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeResolverMock;

    /**
     * @var \Magento\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlMock;

    /**
     * @var \Magento\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Translate\Inline\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Translate\Inline\ParserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parserMock;

    /**
     * @var \Magento\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    protected function setUp()
    {
        $this->scopeResolverMock = $this->getMock('Magento\Framework\App\ScopeResolverInterface', array(), array(), '', false);
        $this->urlMock = $this->getMock('Magento\UrlInterface', array(), array(), '', false);
        $this->layoutMock = $this->getMock('Magento\View\LayoutInterface', array(), array(), '', false);
        $this->configMock = $this->getMock('Magento\Translate\Inline\ConfigInterface', array(), array(), '', false);
        $this->parserMock = $this->getMock('Magento\Translate\Inline\ParserInterface', array(), array(), '', false);
        $this->stateMock = $this->getMock('Magento\Translate\Inline\StateInterface', array(), array(), '', false);
    }

    /**
     * @param bool $isEnabled
     * @param bool $isActive
     * @param bool $isDevAllowed
     * @param bool $result
     * @dataProvider isAllowedDataProvider
     */
    public function testIsAllowed($isEnabled, $isActive, $isDevAllowed, $result)
    {
        $this->prepareIsAllowed($isEnabled, $isActive, $isDevAllowed);

        $model = new Inline(
            $this->scopeResolverMock,
            $this->urlMock,
            $this->layoutMock,
            $this->configMock,
            $this->parserMock,
            $this->stateMock
        );

        $this->assertEquals($result, $model->isAllowed());
        $this->assertEquals($result, $model->isAllowed());
    }

    public function isAllowedDataProvider()
    {
        return array(
            array(true, true, true, true),
            array(true, false, true, false),
            array(true, true, false, false),
            array(true, false, false, false),
            array(false, true, true, false),
            array(false, false, true, false),
            array(false, true, false, false),
            array(false, false, false, false),
        );
    }

    public function testGetParser()
    {
        $model = new Inline(
            $this->scopeResolverMock,
            $this->urlMock,
            $this->layoutMock,
            $this->configMock,
            $this->parserMock,
            $this->stateMock
        );
        $this->assertEquals($this->parserMock, $model->getParser());
    }

    /**
     * @param string|array $body
     * @param string $expected
     * @dataProvider processResponseBodyStripInlineDataProvider
     */
    public function testProcessResponseBodyStripInline($body, $expected)
    {
        $scope = 'admin';
        $this->prepareIsAllowed(false, true, true, $scope);

        $model = new Inline(
            $this->scopeResolverMock,
            $this->urlMock,
            $this->layoutMock,
            $this->configMock,
            $this->parserMock,
            $this->stateMock,
            '',
            '',
            $scope
        );
        $model->processResponseBody($body, true);
        $this->assertEquals($body, $expected);
    }

    public function processResponseBodyStripInlineDataProvider()
    {
        return array(
            array('test', 'test'),
            array('{{{aaaaaa}}{{bbbbb}}{{eeeee}}{{cccccc}}}', 'aaaaaa'),
            array(array('test1', 'test2'), array('test1', 'test2'),),
            array(array('{{{aaaaaa}}', 'test3'), array('{{{aaaaaa}}', 'test3'),),
            array(array('{{{aaaaaa}}{{bbbbb}}', 'test4'), array('{{{aaaaaa}}{{bbbbb}}', 'test4'),),
            array(array('{{{aaaaaa}}{{bbbbb}}{{eeeee}}{{cccccc}}}', 'test5'), array('aaaaaa', 'test5'),),
        );
    }

    /**
     * @param string $scope
     * @param array|string $body
     * @param array|string $expected
     * @dataProvider processResponseBodyDataProvider
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function testProcessResponseBody($scope, $body, $expected)
    {
        $isJson = true;
        if ($scope == 'admin') {
            $this->prepareIsAllowed(true, true, true, $scope);
        }
        $jsonCall = is_array($body) ? 2 * (count($body) + 1)  : 2;
        $this->parserMock->expects(
            $this->exactly($jsonCall)
        )->method(
            'setIsJson'
        )->will(
            $this->returnValueMap(array(
                array($isJson, $this->returnSelf()),
                array(!$isJson, $this->returnSelf()),
            ))
        );
        $this->parserMock->expects(
            $this->exactly(1)
        )->method(
            'processResponseBodyString'
        )->with(
            is_array($body) ? reset($body) : $body
        );
        $this->parserMock->expects(
            $this->exactly(2)
        )->method(
            'getContent'
        )->will(
            $this->returnValue(is_array($body) ? reset($body) : $body)
        );

        $model = new Inline(
            $this->scopeResolverMock,
            $this->urlMock,
            $this->layoutMock,
            $this->configMock,
            $this->parserMock,
            $this->stateMock,
            '',
            '',
            $scope
        );

        $model->processResponseBody($body, $isJson);
        $this->assertEquals($body, $expected);
    }

    public function processResponseBodyDataProvider()
    {
        return array(
            array('admin', 'test', 'test'),
            array('not_admin', 'test1', 'test1'),
        );
    }

    /**
     * @param $scope
     * @param $body
     * @param $expected
     * @dataProvider processResponseBodyGetInlineScriptDataProvider
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function testProcessResponseBodyGetInlineScript($scope, $body, $expected)
    {
        $isJson = true;
        if ($scope == 'admin') {
            $this->prepareIsAllowed(true, true, true, $scope);
        }
        $jsonCall = is_array($body) ? 2 * (count($body) + 1)  : 2;
        $this->parserMock->expects(
            $this->exactly($jsonCall)
        )->method(
                'setIsJson'
            )->will(
                $this->returnValueMap(array(
                        array($isJson, $this->returnSelf()),
                        array(!$isJson, $this->returnSelf()),
                    ))
            );
        $this->parserMock->expects(
            $this->exactly(1)
        )->method(
                'processResponseBodyString'
            )->with(
                is_array($body) ? reset($body) : $body
            );
        $this->parserMock->expects(
            $this->exactly(2)
        )->method(
                'getContent'
            )->will(
                $this->returnValue(is_array($body) ? reset($body) : $body)
            );

        $model = new Inline(
            $this->scopeResolverMock,
            $this->urlMock,
            $this->layoutMock,
            $this->configMock,
            $this->parserMock,
            $this->stateMock,
            '',
            '',
            $scope
        );

        $model->processResponseBody($body, $isJson);
        $this->assertEquals($body, $expected);
    }

    public function processResponseBodyGetInlineScriptDataProvider()
    {
        return array(
            array('admin', 'test', 'test'),
            array('not_admin', 'test1', 'test1'),
        );
    }

    /**
     * @param bool $isEnabled
     * @param bool $isActive
     * @param bool $isDevAllowed
     * @param null|string $scope
     */
    protected function prepareIsAllowed($isEnabled, $isActive, $isDevAllowed, $scope = null)
    {
        $scopeMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', array(), array(), '', false);
        $this->stateMock->expects($this->any())->method('isEnabled')->will($this->returnValue($isEnabled));
        $this->scopeResolverMock->expects(
            $this->once()
        )->method(
            'getScope'
        )->with(
            $scope
        )->will(
            $this->returnValue($scopeMock)
        );

        $this->configMock->expects(
            $this->once()
        )->method(
            'isActive'
        )->with(
            $scopeMock
        )->will(
            $this->returnValue($isActive)
        );

        $this->configMock->expects(
            $this->exactly((int)$isActive)
        )->method(
            'isDevAllowed'
        )->will(
            $this->returnValue($isDevAllowed)
        );
    }
}
