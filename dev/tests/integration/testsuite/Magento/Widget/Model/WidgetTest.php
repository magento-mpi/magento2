<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Widget_Model_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\Widget\Model\Widget');
    }

    public function testGetWidgetsArray()
    {
        $declaredWidgets = $this->_model->getWidgetsArray();
        $this->assertNotEmpty($declaredWidgets);
        $this->assertInternalType('array', $declaredWidgets);
        foreach ($declaredWidgets as $row) {
            $this->assertArrayHasKey('name', $row);
            $this->assertArrayHasKey('code', $row);
            $this->assertArrayHasKey('type', $row);
            $this->assertArrayHasKey('description', $row);
        }
    }

    /**
     * @param string $type
     * @param string $expectedFile
     * @return string
     *
     * @dataProvider getPlaceholderImageUrlDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetPlaceholderImageUrl($type, $expectedFile)
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setDesignTheme('magento_basic', 'adminhtml');
        $expectedPubFile = Mage::getBaseDir(\Magento\Core\Model\Dir::STATIC_VIEW)
            . "/adminhtml/magento_basic/en_US/{$expectedFile}";
        if (file_exists($expectedPubFile)) {
            unlink($expectedPubFile);
        }
        $expectedPubFile = str_replace('/', DIRECTORY_SEPARATOR, $expectedPubFile);
        $url = $this->_model->getPlaceholderImageUrl($type);
        $this->assertStringEndsWith($expectedFile, $url);
        $this->assertFileExists($expectedPubFile);
        return $expectedPubFile;
    }

    /**
     * @return array
     */
    public function getPlaceholderImageUrlDataProvider()
    {
        return array(
            'custom image'  => array(
                'Magento\Catalog\Block\Product\Widget\New',
                'Magento_Catalog/images/product_widget_new.gif'
            ),
            'default image' => array(
                'non_existing_widget_type',
                'Magento_Widget/placeholder.gif'
            ),
        );
    }

    /**
     * Tests, that theme file is found anywhere in theme folders, not only in module directory.
     *
     * @magentoDataFixture Magento/Widget/_files/themes.php
     * @magentoAppIsolation enabled
     */
    public function testGetPlaceholderImageUrlAtTheme()
    {
        Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_APP_DIRS => array(
                \Magento\Core\Model\Dir::THEMES => dirname(__DIR__) . '/_files/design'
            )
        ));
        $actualFile = $this->testGetPlaceholderImageUrl(
            'Magento\Catalog\Block\Product\Widget\New',
            'Magento_Catalog/images/product_widget_new.gif'
        );

        $expectedFile = dirname(__DIR__)
            . '/_files/design/adminhtml/magento_basic/Magento_Catalog/images/product_widget_new.gif';
        $this->assertFileEquals($expectedFile, $actualFile);
    }
}
