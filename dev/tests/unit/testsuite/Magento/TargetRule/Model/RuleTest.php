<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rule
     */
    protected $_rule;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sqlBuilderMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productFactory;

    public function setUp()
    {
        $this->_sqlBuilderMock = $this->getMock('\Magento\Rule\Model\Condition\Sql\Builder', [], [], '', false);

        $this->_productFactory = $this->getMock('\Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $this->_rule = (new ObjectManager($this))->getObject('\Magento\TargetRule\Model\Rule', [
            'context' => $this->_getCleanMock('\Magento\Framework\Model\Context'),
            'registry' => $this->_getCleanMock('\Magento\Framework\Registry'),
            'formFactory' => $this->_getCleanMock('\Magento\Framework\Data\FormFactory'),
            'localeDate' => $this->getMockForAbstractClass(
                    '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
                    [],
                    '',
                    false
                ),
            'ruleFactory' => $this->_getCleanMock('\Magento\TargetRule\Model\Rule\Condition\CombineFactory'),
            'actionFactory' => $this->_getCleanMock('\Magento\TargetRule\Model\Actions\Condition\CombineFactory'),
            'productFactory' => $this->_productFactory,
            'ruleProductIndexerProcessor' => $this->_getCleanMock(
                    '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor'
                ),
            'sqlBuilder' => $this->_sqlBuilderMock,
        ]);
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    public function testPrepareMatchingProducts()
    {
        $productCollection = $this->_getCleanMock('\Magento\Catalog\Model\Resource\Product\Collection');

        $productCollection->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue([1, 2, 3]));

        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getCollection', '__sleep', '__wakeup', 'load', 'getId'],
            [],
            '',
            false
        );

        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($productCollection));

        $productMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());

        $this->_productFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($productMock));

        /** @var \Magento\Rule\Model\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject $conditions */
        $conditions = $this->getMock(
            '\Magento\Rule\Model\Condition\Combine',
            ['collectValidatedAttributes'],
            [],
            '',
            false
        );

        $conditions->expects($this->once())
            ->method('collectValidatedAttributes')
            ->with($productCollection);

        $this->_sqlBuilderMock->expects($this->once())
            ->method('attachConditionToCollection')
            ->with($productCollection, $conditions);

        $this->_rule->setConditions($conditions);
        $this->_rule->prepareMatchingProducts();
        $this->assertEquals([1, 2, 3], $this->_rule->getMatchingProductIds());
    }
}
