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
namespace Magento\Widget\Model;

class WidgetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Widget\Model\Widget');
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->setAreaCode(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\DesignInterface')
            ->setDesignTheme('magento_basic');
        $expectedPubFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')
                ->getDir(\Magento\App\Dir::STATIC_VIEW) . "/adminhtml/magento_basic/en_US/{$expectedFile}";
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
                'Magento\Catalog\Block\Product\Widget\NewWidget',
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
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\App\Dir $dir */
        $dir = $objectManager->get('Magento\App\Dir');

        $property = new \ReflectionProperty($dir, '_dirs');
        $property->setAccessible(true);
        $dirs = $property->getValue($dir);
        $dirs[\Magento\App\Dir::THEMES] = dirname(__DIR__) . '/_files/design';
        $property->setValue($dir, $dirs);

        $actualFile = $this->testGetPlaceholderImageUrl(
            'Magento\Catalog\Block\Product\Widget\NewWidget',
            'Magento_Catalog/images/product_widget_new.gif'
        );

        $expectedFile = dirname(__DIR__)
            . '/_files/design/adminhtml/magento_basic/Magento_Catalog/images/product_widget_new.gif';
        $this->assertFileEquals($expectedFile, $actualFile);
    }
}
