<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

/**
 * Tests for the view layer fallback mechanism
 * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
 */
class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\FileSystem
     */
    protected $_model = null;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
            array(
                \Magento\Framework\App\Filesystem::PARAM_APP_DIRS => array(
                    \Magento\Framework\App\Filesystem::THEMES_DIR => array(
                        'path' => dirname(dirname(__DIR__)) . '/Core/Model/_files/design'
                    )
                )
            )
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')
            ->setAreaCode('frontend');
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\FileSystem'
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setDesignTheme(
            'test_default'
        );
    }

    public function testGetFilename()
    {
        $expected = '%s/frontend/test_default/Magento_Catalog/theme_template.phtml';
        $actual = $this->_model->getFilename('Magento_Catalog::theme_template.phtml', array());
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetFileNameAccordingToLocale()
    {
        $expected = '%s/frontend/test_default/i18n/fr_FR/logo.gif';
        $actual = $this->_model->getLocaleFileName('logo.gif', array('locale' => 'fr_FR'));
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetViewFile()
    {
        $expected = '%s/frontend/vendor_custom_theme/Fixture_Module/fixture_script.js';
        $params = array('theme' => 'vendor_custom_theme');
        $actual = $this->_model->getViewFile('Fixture_Module::fixture_script.js', $params);
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    /**
     * Tests expected vs actual found fallback filename
     *
     * @param string $expected
     * @param string $actual
     */
    protected function _testExpectedVersusActualFilename($expected, $actual)
    {
        $this->assertStringMatchesFormat($expected, $actual);
        $this->assertFileExists($actual);
    }
}
