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
namespace Magento\Core\Model\Theme;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme\Registration
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    /**
     * Initialize base models
     */
    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
            array(
                \Magento\Framework\App\Filesystem::PARAM_APP_DIRS => array(
                    \Magento\Framework\App\Filesystem::THEMES_DIR => array(
                        'path' => dirname(__DIR__) . '/_files/design'
                    )
                )
            )
        );
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Framework\App\AreaList')
            ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            ->load(\Magento\Framework\App\Area::PART_CONFIG);

        $objectManager->get('Magento\Framework\App\State')
            ->setAreaCode(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_theme = $objectManager
            ->create('Magento\View\Design\ThemeInterface');
        $this->_model = $objectManager
            ->create('Magento\Core\Model\Theme\Registration');
    }

    /**
     * Register themes by pattern
     * Use this method only with database isolation
     *
     * @return \Magento\Core\Model\Theme\RegistrationTest
     */
    protected function registerThemes()
    {
        $pathPattern = 'frontend/*/theme.xml';
        $this->_model->register($pathPattern);
        return $this;
    }

    /**
     * Use this method only with database isolation
     *
     * @return \Magento\Core\Model\Theme
     */
    protected function _getTestTheme()
    {
        $theme = $this->_theme->getCollection()->getThemeByFullPath(
            implode(\Magento\View\Design\ThemeInterface::PATH_SEPARATOR, array('frontend', 'test_test_theme'))
        );
        $this->assertNotEmpty($theme->getId());
        return $theme;
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testVirtualByVirtualRelation()
    {
        $this->registerThemes();
        $theme = $this->_getTestTheme();

        $virtualTheme = clone $this->_theme;
        $virtualTheme->setData($theme->getData())->setId(null);
        $virtualTheme->setType(\Magento\View\Design\ThemeInterface::TYPE_VIRTUAL)->save();

        $subVirtualTheme = clone $this->_theme;
        $subVirtualTheme->setData($theme->getData())->setId(null);
        $subVirtualTheme->setParentId(
            $virtualTheme->getId()
        )->setType(
            \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL
        )->save();

        $this->registerThemes();
        $parentId = $subVirtualTheme->getParentId();
        $subVirtualTheme->load($subVirtualTheme->getId());
        $this->assertNotEquals($parentId, $subVirtualTheme->getParentId());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testPhysicalThemeElimination()
    {
        $this->registerThemes();
        $theme = $this->_getTestTheme();

        $testTheme = clone $this->_theme;
        $testTheme->setData($theme->getData())->setThemePath('empty')->setId(null);
        $testTheme->setType(\Magento\View\Design\ThemeInterface::TYPE_PHYSICAL)->save();

        $this->registerThemes();
        $testTheme->load($testTheme->getId());
        $this->assertNotEquals((int)$testTheme->getType(), \Magento\View\Design\ThemeInterface::TYPE_PHYSICAL);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testRegister()
    {
        $this->registerThemes();
        $themePath = implode(\Magento\View\Design\ThemeInterface::PATH_SEPARATOR, array('frontend', 'test_test_theme'));
        $theme = $this->_model->getThemeFromDb($themePath);
        $this->assertEquals($themePath, $theme->getFullPath());
    }
}
