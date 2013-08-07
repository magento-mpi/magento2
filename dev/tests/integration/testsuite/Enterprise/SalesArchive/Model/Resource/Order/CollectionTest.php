<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_SalesArchive
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_SalesArchive_Model_Resource_Order_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testGetSelectCountSql()
    {
        $countSql = Mage::getModel('Enterprise_SalesArchive_Model_Resource_Order_Collection')->getSelectCountSql();
        $this->assertInstanceOf('Magento_DB_Select', $countSql);
    }
}
