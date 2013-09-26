<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Config;

class XsdTest extends \PHPUnit_Framework_TestCase
{

    protected $_xsdFile;

    public function setUp()
    {
        $this->_xsdFile = __DIR__ . "/../../../../../../../../app/code/Magento/Cron/etc/crontab.xsd";
    }

    /**
     * @param string $xmlFile
     * @dataProvider validXmlFileDataProvider
     */
    public function testValidXmlFile($xmlFile)
    {
        $dom = new \DOMDocument();
        $dom->load(__DIR__ . "/_files/{$xmlFile}");
        libxml_use_internal_errors(true);
        $result = $dom->schemaValidate($this->_xsdFile);
        libxml_use_internal_errors(false);
        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function validXmlFileDataProvider()
    {
        return array(
            array('crontab_valid.xml'),
            array('crontab_valid_without_schedule.xml'),
        );
    }

    /**
     * @param string $xmlFile
     * @param array $expectedErrors
     * @dataProvider invalidXmlFileDataProvider
     */
    public function testInvalidXmlFile($xmlFile, $expectedErrors)
    {
        $dom = new \DOMDocument();
        $dom->load(__DIR__ . "/_files/{$xmlFile}");
        libxml_use_internal_errors(true);
        $dom->schemaValidate($this->_xsdFile);
        $errors = libxml_get_errors();

        $actualErrors = array();
        foreach ($errors as $error) {
            $actualErrors[] = $error->message;
        }

        libxml_use_internal_errors(false);
        $this->assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return array
     */
    public function invalidXmlFileDataProvider()
    {
        return array(
            array(
                'crontab_invalid.xml',
                array(
                    "Element 'job', attribute 'wrongName': The attribute 'wrongName' is not allowed.\n",
                    "Element 'job', attribute 'wrongInstance': The attribute 'wrongInstance' is not allowed.\n",
                    "Element 'job', attribute 'wrongMethod': The attribute 'wrongMethod' is not allowed.\n",
                    "Element 'job': The attribute 'name' is required but missing.\n",
                    "Element 'job': The attribute 'instance' is required but missing.\n",
                    "Element 'job': The attribute 'method' is required but missing.\n",
                    "Element 'wrongSchedule': This element is not expected. Expected is ( schedule ).\n",
                )
            ),
            array(
                'crontab_invalid_duplicates.xml',
                array("Element 'job': Duplicate key-sequence ['job1'] in unique identity-constraint 'uniqueJobName'.\n")
            ),
            array(
                'crontab_invalid_without_name.xml',
                array("Element 'job': The attribute 'name' is required but missing.\n")
            ),
            array(
                'crontab_invalid_without_instance.xml',
                array("Element 'job': The attribute 'instance' is required but missing.\n")
            ),
            array(
                'crontab_invalid_without_method.xml',
                array("Element 'job': The attribute 'method' is required but missing.\n")
            )
        );
    }
}
