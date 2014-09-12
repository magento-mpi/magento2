<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action;

use Magento\TestFramework\Helper\ObjectManager;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Row
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productFactory;

    /**
     * @var \Magento\TargetRule\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleFactory;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->_ruleFactory = $this->getMock('\Magento\TargetRule\Model\RuleFactory', ['create'], [], '', false);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Row',
            [
                'productFactory' => $this->getMock('\Magento\Catalog\Model\ProductFactory', [], [], '', false),
                'ruleFactory' => $this->_ruleFactory,
                'ruleCollectionFactory' => $this->getMock(
                        '\Magento\TargetRule\Model\Resource\Rule\CollectionFactory',
                        [],
                        [],
                        '',
                        false
                    ),
                'resource' => $this->getMock('\Magento\TargetRule\Model\Resource\Index', [], [], '', false),
                'storeManager' => $this->getMockForAbstractClass(
                        '\Magento\Framework\StoreManagerInterface',
                        [],
                        '',
                        false
                    ),
                'localeDate' => $this->getMockForAbstractClass(
                        '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
                        [],
                        '',
                        false
                    ),
            ]
        );
    }

    /**
     * @expectedException \Magento\TargetRule\Exception
     * @expectedExceptionMessage Could not rebuild index for undefined product
     */
    public function testEmptyId()
    {
        $this->_model->execute(null);
    }

    public function testCleanProductPagesCache()
    {
        $ruleId = 1;
        $oldProductIds = [1, 2];
        $newProductIds = [2, 3];
        $productsToClean = array_unique(array_merge($oldProductIds, $newProductIds));
        $rule = $this->getMock(
            '\Magento\TargetRule\Model\Rule',
            ['load', 'getResource', 'getMatchingProductIds', 'getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $rule->expects($this->once())->method('load')->with($ruleId);
        $ruleResource = $this->getMock(
            '\Magento\TargetRule\Model\Resource\Rule',
            [
                '__sleep',
                '__wakeup',
                'getAssociatedEntityIds',
                'unbindRuleFromEntity',
                'bindRuleToEntity',
                'cleanCachedDataByProductIds'
            ],
            [],
            '',
            false
        );
        $ruleResource->expects($this->once())
            ->method('getAssociatedEntityIds')
            ->with($ruleId, 'product')
            ->will($this->returnValue($oldProductIds));

        $ruleResource->expects($this->once())
            ->method('unbindRuleFromEntity')
            ->with($ruleId, [], 'product');

        $ruleResource->expects($this->once())
            ->method('bindRuleToEntity')
            ->with($ruleId, $newProductIds, 'product');

        $ruleResource->expects($this->once())
            ->method('cleanCachedDataByProductIds')
            ->with($productsToClean);

        $rule->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $rule->expects($this->once())
            ->method('getMatchingProductIds')
            ->will($this->returnValue($newProductIds));

        $rule->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($ruleResource));

        $this->_ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rule));

        $this->_model->execute($ruleId);
    }
}
