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
 * Test theme virtual model
 */
class Magento_Core_Model_Theme_Domain_VirtualTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get existing staging theme
     *
     * @covers Magento_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeExisting()
    {
        $themeStaging = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false, false);

        $theme = $this->getMock('Magento_Core_Model_Theme', array('getStagingVersion'), array(), '', false, false);
        $theme->expects($this->once())->method('getStagingVersion')->will($this->returnValue($themeStaging));

        $themeFactory = $this->getMock('Magento_Core_Model_ThemeFactory', array('create'), array(), '', false);
        $themeFactory->expects($this->never())->method('create');

        $themeCopyService = $this->getMock('Magento_Core_Model_Theme_CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->never())->method('copy');

        $customizationConfig = $this->getMock('Magento_Theme_Model_Config_Customization', array(), array(), '', false);

        $object = new Magento_Core_Model_Theme_Domain_Virtual(
            $theme, $themeFactory, $themeCopyService, $customizationConfig
        );

        $this->assertSame($themeStaging, $object->getStagingTheme());
        $this->assertSame($themeStaging, $object->getStagingTheme());
    }

    /**
     * Test creating staging theme
     *
     * @covers Magento_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeNew()
    {
        $theme = $this->getMock('Magento_Core_Model_Theme', array('getStagingVersion'), array(), '', false, false);
        $theme->expects($this->once())->method('getStagingVersion')->will($this->returnValue(null));
        /** @var $theme Magento_Object */
        $theme->setData(array(
            'id'                    => 'fixture_theme_id',
            'theme_version'         => 'fixture_theme_version',
            'theme_title'           => 'fixture_theme_title',
            'preview_image'         => 'fixture_preview_image',
            'is_featured'           => 'fixture_is_featured',
            'area'                  => 'fixture_area',
            'type'                  => Magento_Core_Model_Theme::TYPE_VIRTUAL
        ));

        $themeStaging = $this->getMock('Magento_Core_Model_Theme', array('setData', 'save'), array(), '', false, false);
        $themeStaging->expects($this->at(0))->method('setData')->with(array(
            'parent_id'             => 'fixture_theme_id',
            'theme_path'            => null,
            'theme_version'         => 'fixture_theme_version',
            'theme_title'           => 'fixture_theme_title - Staging',
            'preview_image'         => 'fixture_preview_image',
            'is_featured'           => 'fixture_is_featured',
            'area'                  => 'fixture_area',
            'type'                  => Magento_Core_Model_Theme::TYPE_STAGING,
        ));
        $themeStaging->expects($this->at(1))->method('save');

        $themeFactory = $this->getMock('Magento_Core_Model_ThemeFactory', array('create'), array(), '', false);
        $themeFactory->expects($this->once())->method('create')->will($this->returnValue($themeStaging));

        $themeCopyService = $this->getMock('Magento_Core_Model_Theme_CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->once())->method('copy')->with($theme, $themeStaging);

        $customizationConfig = $this->getMock('Magento_Theme_Model_Config_Customization', array(), array(), '', false);

        $object = new Magento_Core_Model_Theme_Domain_Virtual(
            $theme, $themeFactory, $themeCopyService, $customizationConfig
        );

        $this->assertSame($themeStaging, $object->getStagingTheme());
        $this->assertSame($themeStaging, $object->getStagingTheme());
    }

    /**
     * Test for is assigned method
     *
     * @covers Magento_Core_Model_Theme_Domain_Virtual::isAssigned
     */
    public function testIsAssigned()
    {
        $customizationConfig = $this->getMock(
            'Magento_Theme_Model_Config_Customization', array('isThemeAssignedToStore'), array(), '', false
        );
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array('getCollection', 'getId'), array(), '', false,
            false);
        $customizationConfig->expects($this->atLeastOnce())->method('isThemeAssignedToStore')
            ->with($themeMock)
            ->will($this->returnValue(true));
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments('Magento_Core_Model_Theme_Domain_Virtual',
            array(
                 'theme' => $themeMock,
                 'customizationConfig' => $customizationConfig,
            )
        );
        /** @var $model Magento_Core_Model_Theme_Domain_Virtual */
        $model = $objectManagerHelper->getObject('Magento_Core_Model_Theme_Domain_Virtual', $constructArguments);
        $this->assertEquals(true, $model->isAssigned());
    }
}
