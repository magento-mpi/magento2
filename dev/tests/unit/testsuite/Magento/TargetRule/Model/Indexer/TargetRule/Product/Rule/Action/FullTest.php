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

namespace Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action;

class FullTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithAdapterErrorThrowsException()
    {
        $productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array(),
            array(),
            '',
            false
        );

        $ruleFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\RuleFactory',
            array(),
            array(),
            '',
            false
        );

        $collectionFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\Resource\Rule\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );

        $resourceMock = $this->getMock('Magento\TargetRule\Model\Resource\Index', array(), array(), '', false);

        $collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue(array(1, 2)));

        $resourceMock->expects($this->at(2))
            ->method('saveProductIndex')
            ->will($this->returnValue(1));

        $model = new \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full(
            $productFactoryMock,
            $ruleFactoryMock,
            $collectionFactoryMock,
            $resourceMock
        );

        $model->execute();
    }
}
