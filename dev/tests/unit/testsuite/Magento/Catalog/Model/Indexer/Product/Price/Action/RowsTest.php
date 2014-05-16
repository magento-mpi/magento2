<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

use Magento\TestFramework\Helper\ObjectManager;

class RowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Action\Rows
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\Catalog\Model\Indexer\Product\Price\Action\Rows');
    }

    /**
     * @expectedException \Magento\Catalog\Exception
     * @expectedExceptionMessage Bad value was supplied.
     */
    public function testEmptyIds()
    {
        $this->_model->execute(null);
    }
}
