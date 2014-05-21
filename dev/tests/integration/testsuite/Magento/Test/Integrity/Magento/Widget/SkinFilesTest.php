<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Widget;

class SkinFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        /** @var \Magento\Framework\View\Asset\Repository $assetRepo */
        $assetRepo = \Magento\TestFramework\Helper\Bootstrap::getObjectmanager()
            ->get('Magento\Framework\View\Asset\Repository');
        $this->assertFileExists(
            $assetRepo->createAsset($skinImage, array('area' => 'adminhtml'))->getSourceFile()
        );
    }

    /**
     * @return array
     */
    public function widgetPlaceholderImagesDataProvider()
    {
        $result = array();
        /** @var $model \Magento\Widget\Model\Widget */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Widget\Model\Widget');
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance \Magento\Widget\Model\Widget\Instance */
            $instance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                'Magento\Widget\Model\Widget\Instance'
            );
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            if (isset($config['placeholder_image'])) {
                $result[] = array((string)$config['placeholder_image']);
            }
        }
        return $result;
    }
}
