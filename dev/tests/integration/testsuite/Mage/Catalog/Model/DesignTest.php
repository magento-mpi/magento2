<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Catalog_Model_Design.
 */
class Mage_Catalog_Model_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Design
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Catalog_Model_Design');
    }

    /**
     * @dataProvider getThemeModel
     */
    public function testApplyCustomDesign($theme)
    {
        $this->_model->applyCustomDesign($theme);
        $this->assertEquals('package', Mage::getDesign()->getDesignTheme()->getPackageCode());
        $this->assertEquals('theme', Mage::getDesign()->getDesignTheme()->getThemeCode());
    }

    /**
     * @return Mage_Core_Model_Theme
     */
    public function getThemeModel()
    {
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        $theme->setData($this->_getThemeData());
        return array(array($theme));
    }

    /**
     * @return array
     */
    protected function _getThemeData()
    {
        return array(
            'theme_title'          => 'Magento Theme',
            'theme_code'           => 'theme',
            'package_code'         => 'package',
            'theme_path'           => 'package/theme',
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
