<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Model_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Widget
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = new Mage_Widget_Model_Widget;
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
        Mage::getDesign()->setDesignTheme('default/default/default', 'adminhtml');
        $expectedPubFile = Mage::getBaseDir('media') . "/skin/adminhtml/default/default/default/en_US/{$expectedFile}";
        if (file_exists($expectedPubFile)) {
            unlink($expectedPubFile);
        }

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
                'Mage_Catalog_Block_Product_Widget_New',
                'Mage_Catalog/images/product_widget_new.gif'
            ),
            'default image' => array(
                'non_existing_widget_type',
                'Mage_Widget/placeholder.gif'
            ),
        );
    }

    /**
     * Tests, that skin file is found anywhere in skin folders, not only in module directory.
     *
     * @magentoAppIsolation enabled
     */
    public function testGetPlaceholderImageUrlAtSkin()
    {
        Mage::getConfig()->getOptions()->setDesignDir(dirname(__DIR__) . '/_files/design');
        $actualFile = $this->testGetPlaceholderImageUrl(
            'Mage_Catalog_Block_Product_Widget_New',
            'Mage_Catalog/images/product_widget_new.gif'
        );

        $expectedFile = dirname(__DIR__)
            . '/_files/design/adminhtml/default/default/skin/default/Mage_Catalog/images/product_widget_new.gif';
        $this->assertFileEquals($expectedFile, $actualFile);
    }
}
