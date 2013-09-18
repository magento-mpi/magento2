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
class Magento_Test_Integrity_Magento_Widget_SkinFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        $this->assertFileExists(
            Magento_TestFramework_Helper_Bootstrap::getObjectmanager()
                ->get('Magento\Core\Model\View\FileSystem')->getViewFile(
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
        /** @var $model \Magento\Widget\Model\Widget */
        $model = Mage::getModel('Magento\Widget\Model\Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance \Magento\Widget\Model\Widget\Instance */
            $instance = Mage::getModel('Magento\Widget\Model\Widget\Instance');
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            if (isset($config['placeholder_image'])) {
                $result[] = array((string)$config['placeholder_image']);
            }
        }
        return $result;
    }
}
