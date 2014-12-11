<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Action;

class FullTest extends \PHPUnit_Framework_TestCase
{
    public function testFullReindex()
    {
        $ruleFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\RuleFactory',
            [],
            [],
            '',
            false
        );

        $collectionFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\Resource\Rule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $resourceMock = $this->getMock('Magento\TargetRule\Model\Resource\Index', [], [], '', false);

        $collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue([1, 2]));

        $resourceMock->expects($this->at(2))
            ->method('saveProductIndex')
            ->will($this->returnValue(1));

        $storeManagerMock = $this->getMockForAbstractClass('\Magento\Store\Model\StoreManagerInterface');
        $timezoneMock = $this->getMockForAbstractClass('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $model = new \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full(
            $ruleFactoryMock,
            $collectionFactoryMock,
            $resourceMock,
            $storeManagerMock,
            $timezoneMock
        );

        $model->execute();
    }
}
