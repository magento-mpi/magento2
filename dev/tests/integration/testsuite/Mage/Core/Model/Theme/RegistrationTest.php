<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Theme_RegistrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_Registration
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Initialize base models
     */
    protected function setUp()
    {
        $this->_theme = Mage::getModel('Mage_Core_Model_Theme');
        $this->_model = Mage::getModel('Mage_Core_Model_Theme_Registration', array('theme' => $this->_theme));
    }

    /**
     * Register themes by pattern
     * Use this method only with database isolation
     *
     * @return Mage_Core_Model_Theme_RegistrationTest
     */
    protected function registerThemes()
    {
        $basePath = realpath(__DIR__ . '/../_files/design');
        $pathPattern = implode(DIRECTORY_SEPARATOR, array('frontend', '*', '*', 'theme.xml'));
        $this->_model->register($basePath, $pathPattern);
        return $this;
    }

    /**
     * Use this method only with database isolation
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getTestTheme()
    {
        $theme = $this->_theme->getCollection()->getThemeByFullPath(
            implode(Mage_Core_Model_Theme::PATH_SEPARATOR, array('frontend', 'test', 'test_theme'))
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
        $virtualTheme->setType(Mage_Core_Model_Theme::TYPE_VIRTUAL)->save();

        $subVirtualTheme = clone $this->_theme;
        $subVirtualTheme->setData($theme->getData())->setId(null);
        $subVirtualTheme->setParentId($virtualTheme->getId())->setType(Mage_Core_Model_Theme::TYPE_VIRTUAL)->save();

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
        $testTheme->setType(Mage_Core_Model_Theme::TYPE_PHYSICAL)->save();

        $this->registerThemes();
        $testTheme->load($testTheme->getId());
        $this->assertNotEquals((int)$testTheme->getType(), Mage_Core_Model_Theme::TYPE_PHYSICAL);
    }
}