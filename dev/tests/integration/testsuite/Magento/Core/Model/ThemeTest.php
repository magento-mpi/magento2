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
        /** @var $themeModel \Magento\Framework\View\Design\ThemeInterface */
        $themeModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Design\ThemeInterface'
        );
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
            'area' => 'space_area',
            'theme_title' => 'Space theme',
            'theme_version' => '2.0.0.0',
            'parent_id' => null,
            'is_featured' => false,
            'theme_path' => 'default/space',
            'preview_image' => 'images/preview.png',
            'type' => \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL
        );
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\Design\ThemeInterface'
        );
        $collection = $theme->getCollection()
            ->addTypeFilter(\Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL);
        /** @var $currentTheme \Magento\Framework\View\Design\ThemeInterface */
        foreach ($collection as $currentTheme) {
            $parentTheme = $currentTheme->getParentTheme();
            if (!empty($parentTheme)) {
                $this->assertTrue($parentTheme->hasChildThemes());
            }
        }
    }

    /**
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testGetInheritedThemes()
    {
        /** @var \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory */
        $themeFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\Design\Theme\FlyweightFactory'
        );
        $theme = $themeFactory->create('vendor_custom_theme');
        $this->assertCount(2, $theme->getInheritedThemes());
        $expected = array();
        foreach ($theme->getInheritedThemes() as $someTheme) {
            $expected[] = $someTheme->getFullPath();
        }
        $this->assertEquals(array('frontend/vendor_default', 'frontend/vendor_custom_theme'), $expected);
    }
}
