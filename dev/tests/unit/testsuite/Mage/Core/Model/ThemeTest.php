<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Mage_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageFactory;

    protected function setUp()
    {
        /** @var $dirs Mage_Core_Model_Dir|PHPUnit_Framework_MockObject_MockObject */
        $dirs = $this->getMock('Mage_Core_Model_Dir', array('getDir'), array(), '', false);
        $dirs->expects($this->any())->method('getDir')->will($this->returnArgument(0));

        $resourceCollection = $this->getMock('Mage_Core_Model_Resource_Theme_Collection', array(), array(), '', false);
        $this->_imageFactory = $this->getMock('Mage_Core_Model_Theme_ImageFactory', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Mage_Core_Model_Theme',array(
            'dirs'               => $dirs,
            'imageFactory'       => $this->_imageFactory,
            'resourceCollection' => $resourceCollection
        ));

        $this->_model = $objectManagerHelper->getObject('Mage_Core_Model_Theme', $arguments);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    protected function _expectedThemeDataFromConfiguration()
    {
        return array(
            'parent_id'            => null,
            'theme_path'           => 'default/iphone',
            'theme_version'        => '2.0.0.1',
            'theme_title'          => 'Iphone',
            'preview_image'        => 'images/preview.png',
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'is_featured'          => true,
            'theme_directory'      => implode(DIRECTORY_SEPARATOR,
                array(__DIR__, '_files', 'frontend', 'default', 'iphone')),
            'parent_theme_path'    => null,
            'area'                 => 'frontend',
        );
    }

    /**
     * @covers Mage_Core_Model_Theme::getThemeImage
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
     * @covers Mage_Core_Model_Theme::isVirtual
     */
    public function testIsVirtual($type, $isVirtual)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVirtual, $themeModel->isVirtual());
    }

    /**
     * @return array
     */
    public function isVirtualDataProvider()
    {
        return array(
            array('type' => Mage_Core_Model_Theme::TYPE_VIRTUAL, 'isVirtual' => true),
            array('type' => Mage_Core_Model_Theme::TYPE_STAGING, 'isVirtual' => false),
            array('type' => Mage_Core_Model_Theme::TYPE_PHYSICAL, 'isVirtual' => false)
        );
    }

    /**
     * @dataProvider isPhysicalDataProvider
     * @param int $type
     * @param string $isPhysical
     * @covers Mage_Core_Model_Theme::isPhysical
     */
    public function testIsPhysical($type, $isPhysical)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isPhysical, $themeModel->isPhysical());
    }

    /**
     * @return array
     */
    public function isPhysicalDataProvider()
    {
        return array(
            array('type' => Mage_Core_Model_Theme::TYPE_VIRTUAL, 'isPhysical' => false),
            array('type' => Mage_Core_Model_Theme::TYPE_STAGING, 'isPhysical' => false),
            array('type' => Mage_Core_Model_Theme::TYPE_PHYSICAL, 'isPhysical' => true)
        );
    }

    /**
     * @dataProvider isVisibleDataProvider
     * @param int $type
     * @param string $isVisible
     * @covers Mage_Core_Model_Theme::isVisible
     */
    public function testIsVisible($type, $isVisible)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);
        $themeModel->setType($type);
        $this->assertEquals($isVisible, $themeModel->isVisible());
    }

    /**
     * @return array
     */
    public function isVisibleDataProvider()
    {
        return array(
            array('type' => Mage_Core_Model_Theme::TYPE_VIRTUAL, 'isVisible' => true),
            array('type' => Mage_Core_Model_Theme::TYPE_STAGING, 'isVisible' => false),
            array('type' => Mage_Core_Model_Theme::TYPE_PHYSICAL, 'isVisible' => true)
        );
    }

    /**
     * Test id deletable
     *
     * @dataProvider isDeletableDataProvider
     * @param string $themeType
     * @param bool $isDeletable
     * @covers Mage_Core_Model_Theme::isDeletable
     */
    public function testIsDeletable($themeType, $isDeletable)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('getType'), array(), '', false);
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
            array(Mage_Core_Model_Theme::TYPE_VIRTUAL, true),
            array(Mage_Core_Model_Theme::TYPE_STAGING, true),
            array(Mage_Core_Model_Theme::TYPE_PHYSICAL, false)
        );
    }

    public function testIsThemeCompatible()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', null, array(), '', false);

        $themeModel->setMagentoVersionFrom('2.0.0.0')->setMagentoVersionTo('*');
        $this->assertFalse($themeModel->isThemeCompatible());

        $themeModel->setMagentoVersionFrom('1.0.0.0')->setMagentoVersionTo('*');
        $this->assertTrue($themeModel->isThemeCompatible());
    }

    /**
     * @dataProvider checkThemeCompatibleDataProvider
     * @covers Mage_Core_Model_Theme::checkThemeCompatible
     */
    public function testCheckThemeCompatible($versionFrom, $versionTo, $title, $resultTitle)
    {
        $helper = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnValue(sprintf('%s (incompatible version)', $title)));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Mage_Core_Model_Theme', array(
            'helper' => $helper
        ));

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $objectManagerHelper->getObject('Mage_Core_Model_Theme', $arguments);
        $themeModel->setMagentoVersionFrom($versionFrom)->setMagentoVersionTo($versionTo)->setThemeTitle($title);
        $themeModel->checkThemeCompatible();
        $this->assertEquals($resultTitle, $themeModel->getThemeTitle());
    }

    /**
     * @return array
     */
    public function checkThemeCompatibleDataProvider()
    {
        return array(
            array('2.0.0.0', '*', 'Title', 'Title (incompatible version)'),
            array('1.0.0.0', '*', 'Title', 'Title')
        );
    }
}
