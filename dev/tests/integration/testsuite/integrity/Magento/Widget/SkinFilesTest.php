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

class Integrity_Magento_Widget_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        $this->assertFileExists(
            Mage::getObjectmanager()->get('Magento_Core_Model_View_FileSystem')->getViewFile(
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
        /** @var $model Magento_Widget_Model_Widget */
        $model = Mage::getModel('Magento_Widget_Model_Widget');
        foreach ($model->getWidgetsArray() as $row) {
<<<<<<< HEAD:dev/tests/integration/testsuite/integrity/Mage/Widget/SkinFilesTest.php
            /** @var $instance Mage_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            if (isset($config['placeholder_image'])) {
                $result[] = array((string)$config['placeholder_image']);
=======
            /** @var $instance Magento_Widget_Model_Widget_Instance */
            $instance = Mage::getModel('Magento_Widget_Model_Widget_Instance');
            $config = $instance->setType($row['type'])->getWidgetConfig();
            // @codingStandardsIgnoreStart
            if (isset($config->placeholder_image)) {
                $result[] = array((string)$config->placeholder_image);
>>>>>>> upstream/develop:dev/tests/integration/testsuite/integrity/Magento/Widget/SkinFilesTest.php
            }
        }
        return $result;
    }
}
