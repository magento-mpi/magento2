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

/**
 * @group module:Mage_Widget
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
     * @dataProvider getPlaceholderImageUrlDataProvider
     * @magentoAppIsolation enabled
     *
     * @group module:Mage_Catalog
     */
    public function testGetPlaceholderImageUrl($type, $expectedFile)
    {
        Mage::getDesign()->setDesignTheme('default/default/default', 'adminhtml');
        $url = $this->_model->getPlaceholderImageUrl($type);
        $this->assertStringEndsWith($expectedFile, $url);
        $this->assertFileExists(
            Mage::getBaseDir('media') . "/skin/adminhtml/default/default/default/en_US/{$expectedFile}"
        );
    }

    /**
     * @return array
     */
    public function getPlaceholderImageUrlDataProvider()
    {
        return array(
            'custom image'  => array('catalog/product_widget_new', 'Mage_Catalog/images/product_widget_new.gif'),
            'default image' => array('non_existing_widget_type', 'Mage_Widget/placeholder.gif'),
        );
    }
}
