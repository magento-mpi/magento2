<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    public function testToModel()
    {
        $parentId = 1;
        $path = '1/2/3';

        $categoryFactory = $this->getMock('Magento\Catalog\Model\CategoryFactory', ['create'], [], '', false);
        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $categoryMapper = $this->objectManagerHelper->getObject('Magento\Catalog\Service\V1\Data\Category\Mapper',
            [
                'categoryFactory' => $categoryFactory,
                'storeManager' => $storeManager,
            ]
        );

        $store = $this->getMock(
            'Magento\Store\Model\Store',
            ['getRootCategoryId', '__sleep', '__wakeup'], [], '', false
        );
        $store->expects($this->any())->method('getRootCategoryId')->will($this->returnValue($parentId));
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $categoryModel = $this->getMock(
            'Magento\Catalog\Model\Category',
            ['setPath', 'getDefaultAttributeSetId', '__sleep', '__wakeup'], [], '', false
        );
        $parentCategoryModel = $this->getMock(
            'Magento\Catalog\Model\Category',
            ['getPath', 'load', '__sleep', '__wakeup'], [], '', false
        );

        $categoryModel->expects($this->once())->method('setPath')->with($path);

        $parentCategoryModel->expects($this->at(0))->method('load')->will($this->returnSelf());
        $parentCategoryModel->expects($this->at(1))->method('getPath')->will($this->returnValue($path));

        $categoryFactory->expects($this->at(0))->method('create')->will($this->returnValue($categoryModel));
        $categoryFactory->expects($this->at(1))->method('create')->will($this->returnValue($parentCategoryModel));

        $categoryObj = $this->getMock('Magento\Catalog\Service\V1\Data\Category', [], [], '', false);
        $categoryObj->expects($this->any())->method('__toArray')
            ->will($this->returnValue(
                [
                    'test_code' => 'test_value',
                ]
            ));

        $this->assertEquals($categoryModel, $categoryMapper->toModel($categoryObj));
    }
}
