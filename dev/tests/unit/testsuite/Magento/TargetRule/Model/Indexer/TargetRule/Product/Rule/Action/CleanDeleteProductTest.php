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

use Magento\TestFramework\Helper\ObjectManager;

class CleanDeleteProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct'
        );
    }

    /**
     * @expectedException \Magento\TargetRule\Exception
     * @expectedExceptionMessage Could not rebuild index for undefined product
     */
    public function testEmptyIds()
    {
        $this->_model->execute(null);
    }

    public function testCleanDeleteProduct()
    {
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

        $resourceMock->expects($this->once())
            ->method('deleteProductFromIndex')
            ->will($this->returnValue(1));

        $storeManagerMock = $this->getMockForAbstractClass('\Magento\Framework\StoreManagerInterface');
        $timezoneMock = $this->getMockForAbstractClass('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $model = new \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct(
            $ruleFactoryMock,
            $collectionFactoryMock,
            $resourceMock,
            $storeManagerMock,
            $timezoneMock
        );

        $model->execute(2);
    }
}
