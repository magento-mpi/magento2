<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider initDataProvider
     *
     * @param string $area
     * @param string $expectedScope
     */
    public function testInit($area, $expectedScope)
    {
        $localeMock = $this->getMock('\Magento\Core\Model\LocaleInterface');
        $appMock = $this->getMock('\Magento\AppInterface', array(), array(), '', false);
        $appStateMock = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $appStateMock->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue($area));
        $scopeMock = $this->getMock('\Magento\BaseScopeInterface');
        $scopeResolverMock = $this->getMock('\Magento\BaseScopeResolverInterface');
        $scopeResolverMock->expects($this->once())
            ->method('getScope')
            ->will($this->returnValue($scopeMock));
        $themeMock = $this->getMock('\Magento\View\Design\ThemeInterface', array());
        $themeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $designMock = $this->getMock('\Magento\View\DesignInterface');
        $designMock->expects($this->once())
            ->method('getDesignTheme')
            ->will($this->returnValue($themeMock));

        $inlineMock = $this->getMock('\Magento\Translate\InlineInterface');
        $inlineMock->expects($this->at(0))
            ->method('isAllowed')
            ->with()
            ->will($this->returnValue(false));
        $inlineMock->expects($this->at(1))
            ->method('isAllowed')
            ->with($this->equalTo($expectedScope))
            ->will($this->returnValue(true));
        $translateFactoryMock = $this->getMock('\Magento\Translate\Factory', array(), array(), '', false);
        $translateFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($inlineMock));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Backend\Model\Translate $translate */
        $translate = $helper->getObject('Magento\Backend\Model\Translate', array(
            'app' => $appMock,
            'appState' => $appStateMock,
            'scopeResolver' => $scopeResolverMock,
            'viewDesign' => $designMock,
            'translateFactory' => $translateFactoryMock,
            'locale' => $localeMock
        ));
        $translate->init();
    }

    public function initDataProvider()
    {
        return array(
            array('adminhtml', 'admin'),
            array('frontend', null),
        );
    }
}
