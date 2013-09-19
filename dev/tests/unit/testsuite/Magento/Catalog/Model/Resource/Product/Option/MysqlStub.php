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

namespace Magento\Catalog\Model\Resource\Product\Option;

/*
* Extend \Magento\DB\Adapter\Pdo\Mysql and stub needed methods
*/
class MysqlStub extends \Magento\DB\Adapter\Pdo\Mysql
{
    /**
    * Disable parent constructor
    */
    public function __construct()
    {
    }

    /**
    * Stub delete method and add needed asserts
    *
    * @param  string $table
    * @param  array|string $where
    * @return int
    */
    public function delete($table, $where = '')
    {
        \PHPUnit_Framework_TestCase::assertEquals('catalog_product_option_type_title', $table);
        \PHPUnit_Framework_TestCase::assertInternalType('array', $where);
        \PHPUnit_Framework_TestCase::assertEquals(
            ValueTest::$valueTitleData['id'],
            $where['option_type_id = ?']
        );
        \PHPUnit_Framework_TestCase::assertEquals(
            ValueTest::$valueTitleData['store_id'],
            $where['store_id = ?']
        );

        return 0;
    }
}
