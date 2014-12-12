<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action;

use Magento\TestFramework\Helper\ObjectManager;

class RowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Rows
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Rows'
        );
    }

    /**
     * @expectedException \Magento\TargetRule\Exception
     * @expectedExceptionMessage Could not rebuild index for empty products array
     */
    public function testEmptyIds()
    {
        $this->_model->execute(null);
    }
}
