<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Model\Resource\Order;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSelectCountSql()
    {
        $countSql = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\SalesArchive\Model\Resource\Order\Collection'
        )->getSelectCountSql();
        $this->assertInstanceOf('Magento\Framework\DB\Select', $countSql);
    }
}
