<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rule\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ConditionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rule\Model\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->conditionFactory = $this->objectManagerHelper->getObject(
            'Magento\Rule\Model\ConditionFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreate()
    {
        $type = '1';
        $data = ['data2', 'data3'];
        $this->objectManagerMock->expects($this->once())->method('create')->with($type, $data);
        $this->conditionFactory->create($type, $data);
    }
}
