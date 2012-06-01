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
class Mage_ImportExport_Model_Export_Entity_V2_CustomerTest extends PHPUnit_Framework_TestCase
{
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

        $model = new Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer();
        $model->setWriter(new Mage_ImportExport_Model_Export_Adapter_Csv());
        $data = $model->export();
        $this->assertNotEmpty($data);

        $lines = $this->_csvToAssoc($data);
        /** @var $customers Mage_Customer_Model_Customer[] */
        $customers = Mage::registry('_fixture/Mage_ImportExport_Customer_Collection');
        foreach ($customers as $key => $customer) {
            foreach ($expectedAttrCodes as $code) {
                if (!in_array($code, $model->getDisabledAttributes())) {
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
     * Export CSV string to array
     *
     * @param $content
     * @return array
     */
    protected function _csvToAssoc($content)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
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
