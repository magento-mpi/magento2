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

namespace Magento\Catalog\Model\Indexer\Product\Flat;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_model;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $indexerMock = $this->getMock('\Magento\Indexer\Model\Indexer', array('getId'), array(), '', false);
        $indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));

        $flatHelperMock = $this->getMock('\Magento\Catalog\Helper\Product\Flat', array(), array(), '', false);
        $this->_model = $this->_objectManager->getObject('\Magento\Catalog\Model\Indexer\Product\Flat\Processor', array(
            'indexer' => $indexerMock,
            'helper'  => $flatHelperMock
        ));
    }

    public function testGetIndexer()
    {
        $this->assertInstanceOf('\Magento\Indexer\Model\Indexer', $this->_model->getIndexer());
    }
}
