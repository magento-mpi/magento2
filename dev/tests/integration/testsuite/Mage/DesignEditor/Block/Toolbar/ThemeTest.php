<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for theme block functioning
 */
class Mage_DesignEditor_Block_Toolbar_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Theme
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_DesignEditor_Block_Toolbar_Theme');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @dataProvider getThemes
     * @magentoAppIsolation enabled
     */
    public function testIsThemeSelected($themeOld, $themeNew)
    {
        Mage::getDesign()->setDesignTheme($themeOld);
        $isSelected = $this->_block->isThemeSelected($themeOld->getId());
        $this->assertTrue($isSelected);

        Mage::getDesign()->setDesignTheme($themeNew);
        $isSelected = $this->_block->isThemeSelected($themeOld->getId());
        $this->assertFalse($isSelected);
    }

    public function testGetSelectHtmlId()
    {
        $value = $this->_block->getSelectHtmlId();
        $this->assertNotEmpty($value);
    }

    /**
     * @return array
     */
    public function getThemes()
    {
        $oldTheme = $this->_getThemeModel()
            ->setData($this->_getThemeSampleData())
            ->setThemePath('a/b')
            ->setThemeCode('b')
            ->save();

        $newTheme = $this->_getThemeModel()
            ->setData($this->_getThemeSampleData())
            ->setThemePath('c/d')
            ->setThemeCode('d')
            ->save();

        return array(
            array($oldTheme, $newTheme)
        );
    }

    /**
     * @return Mage_Core_Model_Theme
     */
    protected function _getThemeModel()
    {
        return Mage::getModel('Mage_Core_Model_Theme');
    }

    /**
     * @return array
     */
    protected function _getThemeSampleData()
    {
        return array(
            'theme_title'          => 'Default',
            'theme_version'        => '2.0.0.0',
            'parent_theme'         => null,
            'is_featured'          => true,
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'preview_image'        => '',
            'theme_directory'      => implode(
                DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'design', 'frontend', 'default', 'default')
            )
        );
    }
}
