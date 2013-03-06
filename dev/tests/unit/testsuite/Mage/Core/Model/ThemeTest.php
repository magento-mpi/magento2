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
     * Return Mock of Theme Model loaded from configuration
     *
     * @param bool $fromCollection
     * @param string $designDir
     * @param string $targetPath
     * @return Mage_Core_Model_Theme
     */
    protected function _getThemeModel($fromCollection = false, $designDir = '', $targetPath = '')
    {
        /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = $this->getMock('Mage_Core_Model_Resource_Theme_Collection', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Mage_Core_Model_Theme',
            array(
                 'objectManager' => $this->getMock('Magento_ObjectManager', array(), array(), '', false),
                 'themeFactory' => $this->getMock('Mage_Core_Model_Theme_Factory', array(), array(), '', false),
                 'helper' => $this->getMock('Mage_Core_Helper_Data', array(), array(), '', false),
                 'themeImage' => $this->getMock('Mage_Core_Model_Theme_Image', array(), array(), '', false),
                 'resource' => $this->getMock('Mage_Core_Model_Resource_Theme', array(), array(), '', false),
                 'resourceCollection' => $themeCollection
            )
        );
        /** @var $themeMock Mage_Core_Model_Theme */
        $reflection = new \ReflectionClass('Mage_Core_Model_Theme');
        $themeMock = $reflection->newInstanceArgs($arguments);

        if (!$fromCollection) {
            return $themeMock;
        }

        $filesystemMock = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor(true)->getMock();
        $filesystemMock->expects($this->any())->method('searchKeys')
            ->will($this->returnValueMap(array(
                array(
                    $designDir, str_replace('/', DIRECTORY_SEPARATOR, 'frontend/default/iphone/theme.xml'),
                    array(
                        str_replace('/', DIRECTORY_SEPARATOR, $designDir . '/frontend/default/iphone/theme.xml')
                    )
                ),
                array(
                    $designDir, str_replace('/', DIRECTORY_SEPARATOR, 'frontend/default/iphone/theme_invalid.xml'),
                    array(
                        str_replace(
                            '/',
                            DIRECTORY_SEPARATOR,
                            $designDir . '/frontend/default/iphone/theme_invalid.xml'
                        )
                    )
                ),
            )
        ));
        /** @var $dir Mage_Core_Model_Dir */
        $dir = $this->getMockBuilder('Mage_Core_Model_Dir')->disableOriginalConstructor()->getMock();
        /** @var $collectionMock Mage_Core_Model_Theme_Collection|PHPUnit_Framework_MockObject_MockObject */
        $collectionMock = $this->getMock('Mage_Core_Model_Theme_Collection', array('getNewEmptyItem'),
            array($filesystemMock, $dir));
        $collectionMock->expects($this->any())
            ->method('getNewEmptyItem')
            ->will($this->returnValue($themeMock));

        return $collectionMock->setBaseDir($designDir)->addTargetPattern($targetPath)->getFirstItem();
    }

    /**
     * Test load from configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     */
    public function testLoadFromConfiguration()
    {
        $targetPath = implode(DIRECTORY_SEPARATOR, array('frontend', 'default', 'iphone', 'theme.xml'));
        $designDir = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files'));

        $this->assertEquals(
            $this->_expectedThemeDataFromConfiguration(),
            $this->_getThemeModel(true, $designDir, $targetPath)->getData()
        );
    }

    /**
     * Test load invalid configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     * @expectedException Magento_Exception
     */
    public function testLoadInvalidConfiguration()
    {
        $targetPath = implode(DIRECTORY_SEPARATOR, array('frontend', 'default', 'iphone', 'theme_invalid.xml'));
        $designDir = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files'));

        $this->assertEquals(
            $this->_expectedThemeDataFromConfiguration(),
            $this->_getThemeModel(true, $designDir, $targetPath)->getData()
        );
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function _expectedThemeDataFromConfiguration()
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

    public function testSaveThemeCustomization()
    {
        $themeMock = $this->_getThemeModel();
        $jsFile = $this->getMock('Mage_Core_Model_Theme_Customization_Files_Js', array('saveData'), array(), '', false);
        $jsFile->expects($this->atLeastOnce())->method('saveData');

        $themeMock->setCustomization($jsFile);
        $this->assertInstanceOf('Mage_Core_Model_Theme', $themeMock->saveThemeCustomization());
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
     * @param bool $isVirtual
     * @covers Mage_Core_Model_Theme::isDeletable
     */
    public function testIsDeletable($isVirtual)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('isVirtual'), array(), '', false);
        $themeModel->expects($this->once())
            ->method('isVirtual')
            ->will($this->returnValue($isVirtual));
        $this->assertEquals($isVirtual, $themeModel->isDeletable());
    }

    /**
     * @return array
     */
    public function isDeletableDataProvider()
    {
        return array(array(true), array(false));
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
        $themeModel = $this->getMock('Mage_Core_Model_Theme', null, $arguments);
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
