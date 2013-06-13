<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Model_Resource_Role_Grid_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Resource_Role_Grid_Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_User_Model_Resource_Role_Grid_Collection');
    }

    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'role_type' => Mage_User_Model_Acl_Role_Group::ROLE_TYPE,
                'role_name' => Magento_Test_Bootstrap::ADMIN_ROLE_NAME,
            ),
        );
        $actualResult = array();
        /** @var Mage_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
