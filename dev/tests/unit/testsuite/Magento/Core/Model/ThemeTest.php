<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Magento_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageFactory;

    protected function setUp()
    {
        $customizationConfig = $this->getMock('Magento_Theme_Model_Config_Customization', array(), array(), '', false);
        $customizationFactory = $this->getMock('Magento_Core_Model_Theme_CustomizationFactory',
            array('create'), array(), '', false);
        $resourceCollection = $this->getMock(
            'Magento_Core_Model_Resource_Theme_Collection',
            array(),
            array(),
            '',
            false
        );
        $this->_imageFactory = $this->getMock('Magento_Core_Model_Theme_ImageFactory',
            array('create'), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Magento_Core_Model_Theme', array(
            'customizationFactory' => $customizationFactory,
            'customizationConfig'  => $customizationConfig,
            'imageFactory'         => $this->_imageFactory,
            'resourceCollection'   => $resourceCollection
        ));

        $this->_model = $objectManagerHelper->getObject('Magento_Core_Model_Theme', $arguments);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @covers Magento_Core_Model_Theme::getThemeImage
     */
    public function testThemeImageGetter()
    {
        $this->_imageFactory->expects($this->once())->method('create')->with(array('theme' => $this->_model));
        $this->_model->getThemeImage();
    }

    /**
     * @dataProvider isVirtualDataProvider
     * @param int $type
     * @param string $isVirtual
     * @covers Magento_Core_Model_Theme::isVirtual
     */
    public function testIsVirtual($type, $isVirtual)
    {
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = $this->getMock('Magento_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVirtual, $themeModel->isVirtual());
    }

    /**
     * @return array
     */
    public function isVirtualDataProvider()
    {
        return array(
            array('type' => Magento_Core_Model_Theme::TYPE_VIRTUAL, 'isVirtual' => true),
            array('type' => Magento_Core_Model_Theme::TYPE_STAGING, 'isVirtual' => false),
            array('type' => Magento_Core_Model_Theme::TYPE_PHYSICAL, 'isVirtual' => false)
        );
    }

    /**
     * @dataProvider isPhysicalDataProvider
     * @param int $type
     * @param string $isPhysical
     * @covers Magento_Core_Model_Theme::isPhysical
     */
    public function testIsPhysical($type, $isPhysical)
    {
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = $this->getMock('Magento_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isPhysical, $themeModel->isPhysical());
    }

    /**
     * @return array
     */
    public function isPhysicalDataProvider()
    {
        return array(
            array('type' => Magento_Core_Model_Theme::TYPE_VIRTUAL, 'isPhysical' => false),
            array('type' => Magento_Core_Model_Theme::TYPE_STAGING, 'isPhysical' => false),
            array('type' => Magento_Core_Model_Theme::TYPE_PHYSICAL, 'isPhysical' => true)
        );
    }

    /**
     * @dataProvider isVisibleDataProvider
     * @param int $type
     * @param string $isVisible
     * @covers Magento_Core_Model_Theme::isVisible
     */
    public function testIsVisible($type, $isVisible)
    {
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = $this->getMock('Magento_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVisible, $themeModel->isVisible());
    }

    /**
     * @return array
     */
    public function isVisibleDataProvider()
    {
        return array(
            array('type' => Magento_Core_Model_Theme::TYPE_VIRTUAL, 'isVisible' => true),
            array('type' => Magento_Core_Model_Theme::TYPE_STAGING, 'isVisible' => false),
            array('type' => Magento_Core_Model_Theme::TYPE_PHYSICAL, 'isVisible' => true)
        );
    }

    /**
     * Test id deletable
     *
     * @dataProvider isDeletableDataProvider
     * @param string $themeType
     * @param bool $isDeletable
     * @covers Magento_Core_Model_Theme::isDeletable
     */
    public function testIsDeletable($themeType, $isDeletable)
    {
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = $this->getMock('Magento_Core_Model_Theme', array('getType'), array(), '', false);
        $themeModel->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($themeType));
        $this->assertEquals($isDeletable, $themeModel->isDeletable());
    }

    /**
     * @return array
     */
    public function isDeletableDataProvider()
    {
        return array(
            array(Magento_Core_Model_Theme::TYPE_VIRTUAL, true),
            array(Magento_Core_Model_Theme::TYPE_STAGING, true),
            array(Magento_Core_Model_Theme::TYPE_PHYSICAL, false)
        );
    }

    /**
     * @param mixed $originalCode
     * @param string $expectedCode
     * @dataProvider getCodeDataProvider
     */
    public function testGetCode($originalCode, $expectedCode)
    {
        $this->_model->setCode($originalCode);
        $this->assertSame($expectedCode, $this->_model->getCode());
    }

    /**
     * @return array
     */
    public function getCodeDataProvider()
    {
        return array(
            'string code' => array('theme/code', 'theme/code'),
            'null code'   => array(null, ''),
            'number code' => array(10, '10'),
        );
    }
}
