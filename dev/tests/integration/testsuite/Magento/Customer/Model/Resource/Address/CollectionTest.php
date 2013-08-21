<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for customer addresses collection
 */
class Magento_Customer_Model_Resource_Address_CollectionTest extends PHPUnit_Framework_TestCase
{

    public function testSetCustomerFilter()
    {
        $collection = Mage::getModel('Magento_Customer_Model_Resource_Address_Collection');
        $select = $collection->getSelect();
        $this->assertSame($collection, $collection->setCustomerFilter(array(1, 2)));
        $customer = Mage::getObjectManager()->create('Magento_Customer_Model_Customer');
        $collection->setCustomerFilter($customer);
        $customer->setId(3);
        $collection->setCustomerFilter($customer);
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Sparent_id%S IN(%S1%S, %S2%S))%SAND%S(%Sparent_id%S = %S-1%S)%SAND%S(%Sparent_id%S = %S3%S)%A',
            (string)$select
        );
    }
}
