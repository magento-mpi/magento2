<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Theme_Block_Adminhtml_System_Design_Theme_Tab_CssTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_model = $this->getMock(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css',
            array('_getCurrentTheme'),
            $this->_prepareModelArguments(),
            '',
            true
        );
    }

    /**
     * @return array
     */
    protected function _prepareModelArguments()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css',
            array(
                'formFactory'      => $this->getMock('Magento\Data\Form\Factory', array(), array(), '', false),
                'objectManager'   => $this->_objectManager,
                'dirs'            => new \Magento\Core\Model\Dir(__DIR__),
                'uploaderService' => $this->getMock(
                    'Magento\Theme\Model\Uploader\Service', array(), array(), '', false
                ),
                'urlBuilder'      => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false)
            )
        );
        return $constructArguments;
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetUploadCssFileNote()
    {
        $method = self::getMethod('_getUploadCssFileNote');
        /** @var $sizeModel \Magento\File\Size */
        $sizeModel = $this->getMock('Magento\File\Size', null, array(), '', false);

        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with('Magento\File\Size')
            ->will($this->returnValue($sizeModel));

        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = 'Allowed file types *.css.<br />';
        $expectedResult .= 'This file will replace the current custom.css file and can\'t be more than 2 MB.<br />';
        $expectedResult .= sprintf(
            'Max file size to upload %sM',
            $sizeModel->getMaxFileSizeInMb()
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAdditionalElementTypes()
    {
        $method = self::getMethod('_getAdditionalElementTypes');

        /** @var $configModel \Magento\Core\Model\Config */
        $configModel = $this->getMock('Magento\Core\Model\Config', null, array(), '', false);

        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with('Magento\Core\Model\Config')
            ->will($this->returnValue($configModel));

        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = array(
            'links' => 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\Links',
            'css_file' => 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\File'
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('CSS Editor', $this->_model->getTabLabel());
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
