<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

use \Magento\Catalog\Model\Product;

class GalleryEntryResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GalleryEntryResolver
     */
    private $entryResolver;

    protected function setUp()
    {
        $this->entryResolver = new GalleryEntryResolver();
    }

    public function testGetEntryFilePathById()
    {
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->any())->method('getData')->with('media_gallery')->will($this->returnValue(array(
            'images' => array(
                array(
                    'file' => '/i/m/image.jpg',
                    'value_id' => 1,
                ),
                array(
                    'file' => '/i/m/image2.jpg',
                    'value_id' => 2,
                ),
            ),
        )));
        $this->assertEquals('/i/m/image2.jpg', $this->entryResolver->getEntryFilePathById($productMock, 2));
        $this->assertNull($this->entryResolver->getEntryFilePathById($productMock, 9999));
    }

    public function testGetEntryIdByFilePath()
    {
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->any())->method('getData')->with('media_gallery')->will($this->returnValue(array(
            'images' => array(
                array(
                    'file' => '/i/m/image2.jpg',
                    'value_id' => 2,
                ),
                array(
                    'file' => '/i/m/image.jpg',
                    'value_id' => 1,
                ),
            ),
        )));
        $this->assertEquals(1, $this->entryResolver->getEntryIdByFilePath($productMock, '/i/m/image.jpg'));
        $this->assertNull($this->entryResolver->getEntryIdByFilePath($productMock, '/i/m/non_existent_image.jpg'));
    }
}
