<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Action;

use Magento\TestFramework\Helper\ObjectManager;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Action\Rows
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\CatalogInventory\Model\Indexer\Stock\Action\Row');
    }

    /**
     * @expectedException \Magento\CatalogInventory\Exception
     * @expectedExceptionMessage Could not rebuild index for undefined product
     */
    public function testEmptyId()
    {
        $this->_model->execute(null);
    }
}
