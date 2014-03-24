<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Xml;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $filename
     * @dataProvider getXmlFiles
     */
    public function testXmlFiles($filename)
    {
        $dom = new \DOMDocument();
        $xmlFile = file_get_contents($filename);
        $dom->loadXML($xmlFile);
        $errors = libxml_get_errors();
        $this->assertTrue(empty($errors), print_r($errors, true));

        $schemaLocations = [];
        preg_match('/xsi:noNamespaceSchemaLocation=\s*"([^"]+)"/s', $xmlFile, $schemaLocations);
        $this->assertEquals(
            2,
            count($schemaLocations),
            'The XML file at ' . $filename . ' does not have a schema properly defined.  It should
have a xsi:noNamespaceSchemaLocation attribute defined with a relative path.  E.g.
xsi:noNamespaceSchemaLocation="../../../lib/Magento/Framework/etc/something.xsd"
            '
        );

        $schemaFile = dirname($filename).'/'.$schemaLocations[1];

        $this->assertTrue(file_exists($schemaFile), "$filename refers to an invalid schema $schemaFile.");

        $this->assertTrue($dom->schemaValidate($schemaFile), "$filename doesn't validate against $schemaFile");
        $errors = libxml_get_errors();
        $this->assertTrue(empty($errors), "Error validating $filename against $schemaFile\n" . print_r($errors, true));
    }


    public function getSchemas()
    {
        $codeSchemas = $this->_getFiles(BP . '/app/code/Magento', '*.xsd');
        $libSchemas = $this->_getFiles(BP . '/lib/Magento', '*.xsd');
        return $this->_dataSet(array_merge($codeSchemas, $libSchemas));
    }

    public function getXmlFiles()
    {
        $codeXml = $this->_getFiles(BP . '/app/code/Magento', '*.xml');
        $codeXml = array_filter(
            $codeXml,
            function ($item) {
                return strpos($item, "Dhl/etc/countries.xml") == false;
            }
        );
        $designXml = $this->_getFiles(BP . '/app/design', '*.xml');
        $libXml = $this->_getFiles(BP . '/lib/Magento', '*.xml');
        return $this->_dataSet(array_merge($codeXml, $designXml, $libXml));
    }

    protected function _getFiles($dir, $pattern)
    {
        $files = glob($dir . '/' . $pattern, GLOB_NOSORT);
        foreach (glob($dir . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $newDir) {
            $files = array_merge($files, $this->_getFiles($newDir, $pattern));
        }
        return $files;
    }

    protected function _dataSet($files)
    {
        $arrayWrap = function ($item) {
            return [$item];
        };
        return array_combine($files, array_map($arrayWrap, $files));
    }

    public function _getSchemaKey($schemaFilename)
    {
        $key = $schemaFilename;
        $index = strpos($schemaFilename, "Magento");
        if ($index) {
            $key = substr($schemaFilename, $index);
        }
        return $key;
    }
}
