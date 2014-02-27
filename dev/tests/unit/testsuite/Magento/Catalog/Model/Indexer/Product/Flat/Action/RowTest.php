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

namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

use Magento\TestFramework\Helper\ObjectManager;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\Catalog\Model\Indexer\Product\Flat\Action\Row');
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Could not rebuild index for undefined product
     */
    public function testEmptyId()
    {
        $this->_model->execute(null);
    }
}
