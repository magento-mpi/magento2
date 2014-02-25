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

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Plugin\Import
     */
    protected $_model;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $indexerMock = $this->getMock(
            '\Magento\Indexer\Model\Indexer', array('getId', 'invalidate'), array(), '', false
        );
        $indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $indexerMock->expects($this->once())->method('invalidate');

        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\Plugin\Import',
            array('indexer' => $indexerMock)
        );
    }

    public function testAfterImportSource()
    {
        $this->assertEquals(1, $this->_model->afterImportSource(1));
    }
}
