<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for theme model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        Mage::getConfig();
        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Theme');
        $themeModel->setData($this->_getThemeValidData());

        $crud = new Magento_Test_Entity($themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return array(
            'area'                 => 'space_area',
            'theme_title'          => 'Space theme',
            'theme_version'        => '2.0.0.0',
            'parent_id'            => null,
            'is_featured'          => false,
            'theme_path'           => 'default/space',
            'preview_image'        => 'images/preview.png',
            'type'                 => Magento_Core_Model_Theme::TYPE_VIRTUAL
        );
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme Magento_Core_Model_Theme */
        /** @var $currentTheme Magento_Core_Model_Theme */
        $theme = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Theme');
        $collection = $theme->getCollection()->addTypeFilter(Magento_Core_Model_Theme::TYPE_VIRTUAL);
        foreach ($collection as $currentTheme) {
            $parentTheme = $currentTheme->getParentTheme();
            if (!empty($parentTheme)) {
                $this->assertTrue($parentTheme->hasChildThemes());
            }
        }
    }
}
