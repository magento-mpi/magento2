<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Rule\Condition;

use Magento\TestFramework\Helper\ObjectManager;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Combine
     */
    protected $_combine;

    protected $returnArray = array(
        'value' => 'Test',
        'label' => 'Test Conditions'
    );

    public function setUp()
    {
        $attribute = $this->getMock('\Magento\TargetRule\Model\Rule\Condition\Product\Attribute',
            ['getNewChildSelectOptions'],
            [],
            '',
            false
        );

        $attribute->expects($this->any())
            ->method('getNewChildSelectOptions')
            ->will($this->returnValue($this->returnArray));

        $attributesFactory = $this->getMock('\Magento\TargetRule\Model\Rule\Condition\Product\AttributesFactory',
            ['create'],
            [],
            '',
            false
        );

        $attributesFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($attribute));

        $this->_combine = (new ObjectManager($this))->getObject(
            '\Magento\TargetRule\Model\Rule\Condition\Combine',
            [
                'context' => $this->_getCleanMock('\Magento\Rule\Model\Condition\Context'),
                'attributesFactory' => $attributesFactory,
            ]
        );
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

    public function testGetNewChildSelectOptions()
    {
        $result = array(
            '0' => array(
                'value' => '',
                'label' => 'Please choose a condition to add.'
            ),
            '1' => array(
                'value' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                'label' => 'Conditions Combination'
            ),
            '2' => $this->returnArray,
        );

        $this->assertEquals($result, $this->_combine->getNewChildSelectOptions());
    }

    public function testCollectValidatedAttributes()
    {
        $productCollection = $this->_getCleanMock('\Magento\Catalog\Model\Resource\Product\Collection');
        $condition = $this->_getCleanMock('\Magento\TargetRule\Model\Rule\Condition\Combine');

        $condition->expects($this->once())
            ->method('collectValidatedAttributes')
            ->will($this->returnSelf());

        $this->_combine->setConditions(array($condition));

        $this->assertEquals($this->_combine, $this->_combine->collectValidatedAttributes($productCollection));
    }
}
