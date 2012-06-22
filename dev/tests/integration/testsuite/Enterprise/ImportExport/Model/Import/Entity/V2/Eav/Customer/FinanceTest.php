<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
 */
class Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that method returns correct class instance
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_getAttributeCollection()
     */
    public function testGetAttributeCollection()
    {
        $customerFinance = new Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance();
        $method = new ReflectionMethod($customerFinance, '_getAttributeCollection');
        $method->setAccessible(true);

        $this->assertInstanceOf('Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection',
            $method->invoke($customerFinance)
        );
    }
}
