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

class Integrity_Mage_Widget_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        $this->assertFileExists(
            Mage::getObjectmanager()->get('Mage_Core_Model_View_FileSystem')->getViewFile(
                $skinImage,
                array('area' => 'adminhtml')
            )
        );
    }

    /**
     * @return array
     */
    public function widgetPlaceholderImagesDataProvider()
    {
        $result = array();
        /** @var $model Mage_Widget_Model_Widget */
        $model = Mage::getModel('Mage_Widget_Model_Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance Mage_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigInArray();
            if (isset($config['placeholder_image'])) {
                $result[] = array((string)$config['placeholder_image']);
            }
        }
        return $result;
    }
}
