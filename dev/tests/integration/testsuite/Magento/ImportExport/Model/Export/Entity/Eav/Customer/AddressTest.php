<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customer address export model
 *
 * @magentoDataFixture Magento/ImportExport/_files/customer_with_addresses.php
 */
class Magento_ImportExport_Model_Export_Entity_Eav_Customer_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address
     */
    protected $_model;

    /**
     * List of existing websites
     *
     * @var array
     */
    protected $_websites = array();

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address');

        $websites = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getWebsites(true);
        /** @var $website Magento_Core_Model_Website */
        foreach ($websites as $website) {
            $this->_websites[$website->getId()] = $website->getCode();
        }
    }

    /**
     * Test export method
     */
    public function testExport()
    {
        $websiteCode  = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_WEBSITE;
        $emailCode    = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_EMAIL;
        $entityIdCode = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID;

        $expectedAttributes = array();
        /** @var $collection Magento_Customer_Model_Resource_Address_Attribute_Collection */
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Resource_Address_Attribute_Collection');
        /** @var $attribute Magento_Customer_Model_Attribute */
        foreach ($collection as $attribute) {
            $expectedAttributes[] = $attribute->getAttributeCode();
        }

        // Get customer default addresses column name to customer attribute mapping array.
        $defaultAddressMap
            = Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::getDefaultAddressAttributeMapping();

        $this->_model->setWriter(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Adapter_Csv'));
        $this->_model->setParameters(array());

        $data = $this->_csvToArray($this->_model->export(), $entityIdCode);

        $this->assertEquals(
            count($expectedAttributes),
            count(array_intersect($expectedAttributes, $data['header'])),
            'Expected attribute codes were not exported'
        );

        $this->assertNotEmpty($data['data'], 'No data was exported');

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        // Get addresses
        /** @var $customers Magento_Customer_Model_Customer[] */
        $customers = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_ImportExport_Customers_Array');
        foreach ($customers as $customer) {
            /** @var $address Magento_Customer_Model_Address */
            foreach ($customer->getAddresses() as $address) {
                // Check unique key
                $data['data'][$address->getId()][$websiteCode] = $this->_websites[$customer->getWebsiteId()];
                $data['data'][$address->getId()][$emailCode] = $customer->getEmail();
                $data['data'][$address->getId()][$entityIdCode] = $address->getId();

                // Check by expected attributes
                foreach ($expectedAttributes as $code) {
                    if (!in_array($code, $this->_model->getDisabledAttributes())) {
                        $this->assertEquals(
                            $address->getData($code),
                            $data['data'][$address->getId()][$code],
                            'Attribute "' . $code . '" is not equal'
                        );
                    }
                }

                // Check customer default addresses column name to customer attribute mapping array
                foreach ($defaultAddressMap as $exportCode => $code) {
                    $this->assertEquals(
                        $address->getData($code),
                        (int) $data['data'][$address->getId()][$exportCode],
                        'Attribute "' . $code . '" is not equal'
                    );
                }
            }
        }
    }

    /**
     * Get possible gender values for filter
     *
     * @return array
     */
    public function getGenderFilterValueDataProvider()
    {
        return array(
            'male'   => array('$genderFilterValue' => 1),
            'female' => array('$genderFilterValue' => 2)
        );
    }

    /**
     * Test export method if filter was set
     *
     * @dataProvider getGenderFilterValueDataProvider
     *
     * @param int $genderFilterValue
     */
    public function testExportWithFilter($genderFilterValue)
    {
        $entityIdCode = Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID;

        $this->_model->setWriter(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Adapter_Csv'));

        $filterData = array(
            'export_filter' => array(
                'gender' => $genderFilterValue
            )
        );

        $this->_model->setParameters($filterData);

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        // Get expected address count
        /** @var $customers Magento_Customer_Model_Customer[] */
        $customers = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_ImportExport_Customers_Array');
        $expectedCount = 0;
        foreach ($customers as $customer) {
            if ($customer->getGender() == $genderFilterValue) {
                $expectedCount += count($customer->getAddresses());
            }
        }

        $data = $this->_csvToArray($this->_model->export(), $entityIdCode);

        $this->assertCount($expectedCount, $data['data']);
    }

    /**
     * Test entity type code value
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer_address', $this->_model->getEntityTypeCode());
    }

    /**
     * Test type of attribute collection
     */
    public function testGetAttributeCollection()
    {
        $this->assertInstanceOf('Magento_Customer_Model_Resource_Address_Attribute_Collection',
            $this->_model->getAttributeCollection()
        );
    }

    /**
     * Export CSV string to array
     *
     * @param string $content
     * @param mixed $entityId
     * @return array
     */
    protected function _csvToArray($content, $entityId = null)
    {
        $data = array(
            'header' => array(),
            'data'   => array()
        );

        $lines = str_getcsv($content, "\n");
        foreach ($lines as $index => $line) {
            if ($index == 0) {
                $data['header'] = str_getcsv($line);
            } else {
                $row = array_combine($data['header'], str_getcsv($line));
                if (!is_null($entityId) && !empty($row[$entityId])) {
                    $data['data'][$row[$entityId]] = $row;
                } else {
                    $data['data'][] = $row;
                }
            }
        }

        return $data;
    }

    /**
     * Test filename getter. Filename must be set in constructor.
     */
    public function testGetFileName()
    {
        $this->assertEquals($this->_model->getEntityTypeCode(), $this->_model->getFileName());
    }
}
