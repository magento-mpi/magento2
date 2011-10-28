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
 * @group integrity
 */
class Integrity_Mage_Widget_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        $this->assertFileExists(Mage::getDesign()->getSkinFile($skinImage, array('_area' => 'adminhtml')));
    }

    /**
     * @return array
     */
    public function widgetPlaceholderImagesDataProvider()
    {
        $result = array();
        $model = new Mage_Widget_Model_Widget;
        foreach ($model->getWidgetsArray() as $row) {
            $instance = new Mage_Widget_Model_Widget_Instance;
            $config = $instance->setType($row['type'])->getWidgetConfig();
            // @codingStandardsIgnoreStart
            if (isset($config->placeholder_image)) {
                $result[] = array((string)$config->placeholder_image);
            }
            // @codingStandardsIgnoreEnd
        }
        return $result;
    }
}
