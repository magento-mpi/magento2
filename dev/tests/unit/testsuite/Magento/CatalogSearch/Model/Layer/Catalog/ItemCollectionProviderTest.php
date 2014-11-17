<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer\Catalog;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ItemCollectionProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollection()
    {
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);

        $collectionMock = $this->getMock('Magento\Catalog\Model\Resource\Product\Collection', [], [], '', false);
        $collectionMock->expects($this->once())->method('addCategoryFilter')->with($categoryMock);

        $collectionFactoryMock = $this->getMock('Magento\Catalog\Model\Resource\Product\CollectionFactory', ['create']);
        $collectionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($collectionMock));

        $objectManager = new ObjectManagerHelper($this);
        $provider = $objectManager->getObject(
            'Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider',
            ['collectionFactory' => $collectionFactoryMock]
        );

        $provider->getCollection($categoryMock);
    }
}
