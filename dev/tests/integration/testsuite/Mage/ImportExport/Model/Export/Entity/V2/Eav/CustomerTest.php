<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customer export model V2
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_V2_Eav_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer();
    }

    protected function tearDown()
    {
        unset($this->_model);
        parent::tearDown();
    }

    /**
     * Test export method
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers.php
     */
    public function testExport()
    {
        $expectedAttrCodes = array();
        /** @var $collection Mage_Customer_Model_Resource_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        /** @var $attribute Mage_Customer_Model_Attribute */
        foreach ($collection as $attribute) {
            $expectedAttrCodes[] = $attribute->getAttributeCode();
        }

        $this->_model->setWriter(new Mage_ImportExport_Model_Export_Adapter_Csv());
        $data = $this->_model->export();
        $this->assertNotEmpty($data);

        $lines = $this->_csvToAssoc($data);
        /** @var $customers Mage_Customer_Model_Customer[] */
        $customers = Mage::registry('_fixture/Mage_ImportExport_Customer_Collection');
        foreach ($customers as $key => $customer) {
            foreach ($expectedAttrCodes as $code) {
                if (!in_array($code, $this->_model->getDisabledAttributes())) {
                    $this->assertEquals(
                        $customer->getData($code),
                        $lines[$key][$code],
                        'Attribute "' . $code . '" is not equal'
                    );
                }
            }
        }
    }

    /**
     * Test entity type code value
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer', $this->_model->getEntityTypeCode());
    }

    /**
     * Test type of attribute collection
     */
    public function testGetAttributeCollection()
    {
        $this->assertInstanceOf('Mage_Customer_Model_Resource_Attribute_Collection',
            $this->_model->getAttributeCollection());
    }

    /**
     * Test for method filterAttributeCollection()
     */
    public function testFilterAttributeCollection()
    {
        /** @var $collection Mage_Customer_Model_Resource_Attribute_Collection */
        $collection = $this->_model->getAttributeCollection();
        $collection = $this->_model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttrs = array();
        /** @var $attribute Mage_Customer_Model_Attribute */
        foreach ($collection as $attribute) {
            $existedAttrs[] = $attribute->getAttributeCode();
        }
        $disabledAttrs = $this->_model->getDisabledAttributes();
        foreach ($disabledAttrs as $attributeCode) {
            $this->assertNotContains(
                $attributeCode,
                $existedAttrs,
                'Disabled attribute "' . $attributeCode . '" existed in collection'
            );
        }
        /**
         * Check that all overridden attributes were affected during filtering process
         */
        $overriddenAttrs = $this->_model->getOverriddenAttributes();
        foreach ($collection as $attribute) {
            if (isset($overriddenAttrs[$attribute->getAttributeCode()])) {
                foreach ($overriddenAttrs[$attribute->getAttributeCode()] as $propertyKey => $property) {
                    $this->assertEquals(
                        $property,
                        $attribute->getData($propertyKey),
                        'Value of property "' . $propertyKey . '" is not equals'
                    );
                }
            }
        }
    }

    /**
     * Export CSV string to array
     *
     * @param $content
     * @return array
     */
    protected function _csvToAssoc($content)
    {
        $tempFile = tempnam(Mage::getBaseDir('tmp'), 'export_');
        $tempFileHandler = fopen($tempFile, 'w+');
        fputs($tempFileHandler, $content);
        fclose($tempFileHandler);

        $tempFileHandler = fopen($tempFile, 'r');
        $lines = array();
        while ($line = fgetcsv($tempFileHandler)) {
            $lines[] = $line;
        }

        $result = array();
        for ($i = 1; $i < count($lines); $i++) {
            $row = array();
            foreach ($lines[$i] as $key => $value) {
                $row[$lines[0][$key]] = $value;
            }
            $result[] = $row;
        }

        return $result;
    }
}
