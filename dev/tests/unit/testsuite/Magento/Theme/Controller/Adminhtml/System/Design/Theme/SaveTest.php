<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

class SaveTest extends \Magento\Theme\Controller\Adminhtml\System\Design\ThemeTest
{
    /**
     * @var string
     */
    protected $name = 'Save';

    /**
     * @covers \Magento\Theme\Controller\Adminhtml\System\Design\Theme::saveAction
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSaveAction()
    {
        $themeData = array('theme_id' => 123);
        $customCssContent = 'custom css content';
        $jsRemovedFiles = array(3, 4);
        $jsOrder = array(1 => '1', 2 => 'test');

        $this->_request->expects(
            $this->at(0)
        )->method(
                'getParam'
            )->with(
                'back',
                false
            )->will(
                $this->returnValue(true)
            );

        $this->_request->expects(
            $this->at(1)
        )->method(
                'getParam'
            )->with(
                'theme'
            )->will(
                $this->returnValue($themeData)
            );
        $this->_request->expects(
            $this->at(2)
        )->method(
                'getParam'
            )->with(
                'custom_css_content'
            )->will(
                $this->returnValue($customCssContent)
            );
        $this->_request->expects(
            $this->at(3)
        )->method(
                'getParam'
            )->with(
                'js_removed_files'
            )->will(
                $this->returnValue($jsRemovedFiles)
            );
        $this->_request->expects(
            $this->at(4)
        )->method(
                'getParam'
            )->with(
                'js_order'
            )->will(
                $this->returnValue($jsOrder)
            );
        $this->_request->expects($this->once(5))->method('getPost')->will($this->returnValue(true));

        $themeMock = $this->getMock(
            'Magento\Core\Model\Theme',
            array('save', 'load', 'setCustomization', 'getThemeImage', '__wakeup'),
            array(),
            '',
            false
        );

        $themeImage = $this->getMock('Magento\Core\Model\Theme\Image', array(), array(), '', false);
        $themeMock->expects($this->any())->method('getThemeImage')->will($this->returnValue($themeImage));

        $themeFactory = $this->getMock(
            'Magento\Framework\View\Design\Theme\FlyweightFactory',
            array('create'),
            array(),
            '',
            false
        );
        $themeFactory->expects($this->once())->method('create')->will($this->returnValue($themeMock));

        $this->_objectManagerMock->expects(
            $this->at(0)
        )->method(
                'get'
            )->with(
                'Magento\Framework\View\Design\Theme\FlyweightFactory'
            )->will(
                $this->returnValue($themeFactory)
            );

        $this->_objectManagerMock->expects(
            $this->at(1)
        )->method(
                'get'
            )->with(
                'Magento\Theme\Model\Theme\Customization\File\CustomCss'
            )->will(
                $this->returnValue(null)
            );

        $this->_objectManagerMock->expects(
            $this->at(2)
        )->method(
                'create'
            )->with(
                'Magento\Theme\Model\Theme\SingleFile'
            )->will(
                $this->returnValue(null)
            );

        $this->_model->execute();
    }
}
