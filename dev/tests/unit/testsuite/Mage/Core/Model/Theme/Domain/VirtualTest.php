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
 * Test theme virtual model
 */
class Mage_Core_Model_Theme_Domain_VirtualTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get existing staging theme
     *
     * @covers Mage_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeExisting()
    {
        $themeStaging = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false, false);

        $theme = $this->getMock('Mage_Core_Model_Theme', array('getStagingVersion'), array(), '', false, false);
        $theme->expects($this->once())->method('getStagingVersion')->will($this->returnValue($themeStaging));

        $themeFactory = $this->getMock('Mage_Core_Model_Theme_Factory', array('create'), array(), '', false);
        $themeFactory->expects($this->never())->method('create');

        $themeCopyService = $this->getMock('Mage_Core_Model_Theme_CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->never())->method('copy');

        $object = new Mage_Core_Model_Theme_Domain_Virtual($theme, $themeFactory, $themeCopyService);

        $this->assertSame($themeStaging, $object->getStagingTheme());
        $this->assertSame($themeStaging, $object->getStagingTheme());
    }

    /**
     * Test creating staging theme
     *
     * @covers Mage_Core_Model_Theme_Domain_Virtual::getStagingTheme
     */
    public function testGetStagingThemeNew()
    {
        $theme = $this->getMock('Mage_Core_Model_Theme', array('getStagingVersion'), array(), '', false, false);
        $theme->expects($this->once())->method('getStagingVersion')->will($this->returnValue(null));
        /** @var $theme Varien_Object */
        $theme->setData(array(
            'id'                    => 'fixture_theme_id',
            'theme_version'         => 'fixture_theme_version',
            'theme_title'           => 'fixture_theme_title',
            'preview_image'         => 'fixture_preview_image',
            'magento_version_from'  => 'fixture_magento_version_from',
            'magento_version_to'    => 'fixture_magento_version_to',
            'is_featured'           => 'fixture_is_featured',
            'area'                  => 'fixture_area',
            'type'                  => Mage_Core_Model_Theme::TYPE_VIRTUAL
        ));

        $themeStaging = $this->getMock('Mage_Core_Model_Theme', array('setData', 'save'), array(), '', false, false);
        $themeStaging->expects($this->at(0))->method('setData')->with(array(
            'parent_id'             => 'fixture_theme_id',
            'theme_path'            => null,
            'theme_version'         => 'fixture_theme_version',
            'theme_title'           => 'fixture_theme_title - Staging',
            'preview_image'         => 'fixture_preview_image',
            'magento_version_from'  => 'fixture_magento_version_from',
            'magento_version_to'    => 'fixture_magento_version_to',
            'is_featured'           => 'fixture_is_featured',
            'area'                  => 'fixture_area',
            'type'                  => Mage_Core_Model_Theme::TYPE_STAGING,
        ));
        $themeStaging->expects($this->at(1))->method('save');

        $themeFactory = $this->getMock('Mage_Core_Model_Theme_Factory', array(), array(), '', false);
        $themeFactory->expects($this->once())->method('create')->will($this->returnValue($themeStaging));

        $themeCopyService = $this->getMock('Mage_Core_Model_Theme_CopyService', array('copy'), array(), '', false);
        $themeCopyService->expects($this->once())->method('copy')->with($theme, $themeStaging);

        $object = new Mage_Core_Model_Theme_Domain_Virtual($theme, $themeFactory, $themeCopyService);

        $this->assertSame($themeStaging, $object->getStagingTheme());
        $this->assertSame($themeStaging, $object->getStagingTheme());
    }
}
