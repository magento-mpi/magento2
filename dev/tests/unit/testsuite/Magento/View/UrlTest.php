<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\UrlInterface;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Design\ThemeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $theme;

    /**
     * @param string $fileId
     * @param array $params
     * @param string $expectedModule
     * @param string $isSecureExpected
     * @dataProvider getViewFileUrlDataProvider
     */
    public function testGetViewFileUrl($fileId, $params, $expectedModule, $isSecureExpected)
    {
        $expectedResult = 'http://example.com/some/context/relative/path/file.ext';
        $service = $this->getMock('\Magento\View\service', array(), array(), '', false);
        $service->expects($this->once())
            ->method('updateDesignParams')
            ->will($this->returnCallback(array($this, 'updateDesignParams')))
        ;
        $baseUrl = $this->getMockForAbstractClass('\Magento\UrlInterface');
        $baseUrl->expects($this->once())
            ->method('getBaseUrl')
            ->with(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecureExpected))
            ->will($this->returnValue('http://example.com/'))
        ;
        $this->theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $path = $this->getMock('\Magento\View\Path');
        $path->expects($this->once())
            ->method('getRelativePath')
            ->with('frontend', $this->theme, 'en_US', $expectedModule)
            ->will($this->returnValue('some/context/relative/path'))
        ;
        $object = new Url($service, $baseUrl, $path);
        $this->assertEquals($expectedResult, $object->getViewFileUrl($fileId, $params));
    }

    /**
     * A mock callback replacement for "update design params" of View Service model
     *
     * @param array $params
     */
    public function updateDesignParams(array &$params)
    {
        $params['area'] = 'frontend';
        $params['locale'] = 'en_US';
        $params['themeModel'] = $this->theme;
    }

    /**
     * @return array
     */
    public function getViewFileUrlDataProvider()
    {
        return array(
            'non-modular file, insecure URL' => array('file.ext', array(), '', false),
            'modular file, insecure URL'     => array('Module::file.ext', array(), 'Module', false),
            'non-modular file, secure URL'   => array('file.ext', array('_secure' => true), '', true),
        );
    }
}
