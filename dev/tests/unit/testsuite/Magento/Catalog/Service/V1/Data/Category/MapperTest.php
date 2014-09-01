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
        $categoryFactory = $this->getMock('Magento\Catalog\Model\CategoryFactory', ['create'], [], '', false);
        $categoryMapper = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Service\V1\Data\Category\Mapper',
            [
                'categoryFactory' => $categoryFactory
            ]
        );

        $categoryModel = $this->getMock(
            'Magento\Catalog\Model\Category',
            ['setPath', 'getDefaultAttributeSetId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );

        $categoryFactory->expects($this->at(0))->method('create')->will($this->returnValue($categoryModel));

        $categoryObj = $this->getMock('Magento\Catalog\Service\V1\Data\Category', [], [], '', false);
        $categoryObj->expects($this->any())->method('__toArray')
            ->will(
                $this->returnValue(
                    [
                        'test_code' => 'test_value',
                    ]
                )
            );

        $this->assertEquals($categoryModel, $categoryMapper->toModel($categoryObj));
    }
}
