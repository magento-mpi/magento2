<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class CustomerGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Plugin\CustomerGroup
     */
    protected $_model;

    /**
     * @var \Magento\Code\Plugin\InvocationChain
     */
    protected $_invocationChainMock;

    /**
     * @var array
     */
    protected $_invocationArguments = array(1);

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_invocationChainMock = $this->getMock(
            '\Magento\Code\Plugin\InvocationChain', array('proceed'), array(), '', false
        );

        $this->_invocationChainMock
            ->expects($this->any())
            ->method('proceed')
            ->with($this->_invocationArguments)
            ->will($this->returnValue(1));

        $indexerMock = $this->getMock(
            '\Magento\Indexer\Model\Indexer', array('getId', 'invalidate'), array(), '', false
        );
        $indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $indexerMock->expects($this->once())->method('invalidate');

        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\Plugin\CustomerGroup',
            array('indexer' => $indexerMock)
        );
    }

    public function testAroundDelete()
    {
        $this->assertEquals(1, $this->_model->aroundDelete($this->_invocationArguments, $this->_invocationChainMock));
    }

    public function testAroundSave()
    {
        $this->assertEquals(1, $this->_model->aroundSave($this->_invocationArguments, $this->_invocationChainMock));
    }
}
