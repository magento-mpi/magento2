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

namespace Magento\Core\Model;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for theme model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        /** @var $themeModel \Magento\Core\Model\Theme */
        $themeModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Core\Model\Theme');
        $themeModel->setData($this->_getThemeValidData());

        $crud = new \Magento\TestFramework\Entity($themeModel, array('theme_version' => '2.0.0.1'));
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
            'type'                 => \Magento\Core\Model\Theme::TYPE_VIRTUAL
        );
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme \Magento\Core\Model\Theme */
        /** @var $currentTheme \Magento\Core\Model\Theme */
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Theme');
        $collection = $theme->getCollection()->addTypeFilter(\Magento\Core\Model\Theme::TYPE_VIRTUAL);
        foreach ($collection as $currentTheme) {
            $parentTheme = $currentTheme->getParentTheme();
            if (!empty($parentTheme)) {
                $this->assertTrue($parentTheme->hasChildThemes());
            }
        }
    }
}
