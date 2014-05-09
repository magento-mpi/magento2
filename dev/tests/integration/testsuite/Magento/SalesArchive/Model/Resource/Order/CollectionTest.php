<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
