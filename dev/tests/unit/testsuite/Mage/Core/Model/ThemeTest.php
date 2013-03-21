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

        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $dirMock->expects($this->any())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::MEDIA)
            ->will($this->returnValue('media'));
        $objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Dir')
            ->will($this->returnValue($dirMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments('Mage_Core_Model_Theme',
            array(
                 'objectManager' => $objectManager,
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

        /** @var $collectionMock Mage_Core_Model_Theme_Collection|PHPUnit_Framework_MockObject_MockObject */
        $collectionMock = $this->getMock('Mage_Core_Model_Theme_Collection', array('getNewEmptyItem'),
            array($filesystemMock));
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
     * @param $customizationPath
     * @param $themeId
     * @param $expected
     * @dataProvider getCustomViewConfigDataProvider
     */
    public function testGetCustomViewConfigPath($customizationPath, $themeId, PHPUnit_Framework_Constraint $expected)
    {
        $themeMock = $this->_getThemeModel();
        $themeMock->setData('customization_path', $customizationPath);
        $themeMock->setId($themeId);
        $actual = $themeMock->getCustomViewConfigPath();
        $this->assertThat($actual, $expected);
    }

    /**
     * @return array
     */
    public function getCustomViewConfigDataProvider()
    {
        return array(
            'no custom path, theme is not loaded' => array(null, null, $this->isEmpty()),
            'no custom path, theme is loaded' => array(null, 'theme_id',
                $this->equalTo('media' . DIRECTORY_SEPARATOR . 'theme_customization' . DIRECTORY_SEPARATOR . 'theme_id'
                . DIRECTORY_SEPARATOR . 'view.xml')
            ),
            'with custom path, theme is not loaded' => array('custom/path', null,
                $this->equalTo('custom/path' . DIRECTORY_SEPARATOR . 'view.xml')),
            'with custom path, theme is loaded' => array('custom/path', 'theme_id',
                $this->equalTo('custom/path' . DIRECTORY_SEPARATOR . 'view.xml')
            ),
        );
    }
}
