<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Model\Product;

/**
 * Class \Magento\Catalog\Model\Product\ImageTest
 * @magentoAppArea frontend
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Magento\Catalog\Model\Product\Image
     */
    public function testSetBaseFilePlaceholder()
    {
        /** @var $model \Magento\Catalog\Model\Product\Image */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product\Image');
        $model->setDestinationSubdir('image')->setBaseFile('');
        $this->assertEmpty($model->getBaseFile());
        return $model;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testSaveFilePlaceholder($model)
    {
        $processor = $this->getMock('Magento\Image', array('save'), array(), '', false);
        $processor->expects($this->exactly(0))->method('save');
        $model->setImageProcessor($processor)->saveFile();
    }

    /**
     * @param \Magento\Catalog\Model\Product\Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testGetUrlPlaceholder($model)
    {
        $this->assertStringMatchesFormat(
            'http://localhost/pub/static/frontend/%s/Magento_Catalog/images/product/placeholder/image.jpg',
            $model->getUrl()
        );
    }

    public function testSetWatermark()
    {
        $inputFile = 'watermark.png';
        $expectedFile = '/somewhere/watermark.png';

        $asset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $asset->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue($expectedFile));
        $assetRepo = $this->getMock('\Magento\View\Asset\Repository', array(), array(), '', false);
        $assetRepo->expects($this->once())
            ->method('createAsset')
            ->with($inputFile)
            ->will($this->returnValue($asset));

        /** @var $model \Magento\Catalog\Model\Product\Image */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product\Image', ['assetRepo' => $assetRepo]);
        $processor = $this->getMock(
            'Magento\Image',
            ['save', 'keepAspectRatio', 'keepFrame', 'keepTransparency', 'constrainOnly', 'backgroundColor', 'quality',
                'setWatermarkPosition', 'setWatermarkImageOpacity', 'setWatermarkWidth', 'setWatermarkHeight',
                'watermark'],
            array(), '', false);
        $processor->expects($this->once())
            ->method('watermark')
            ->with($expectedFile);
        $model->setImageProcessor($processor);

        $model->setWatermark('watermark.png');
    }
}
