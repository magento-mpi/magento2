<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Layout_FilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    public function setUp()
    {
        $this->_schemaFile = Utility_Files::init()->getModuleFile(
            'Magento', 'Core', 'etc' . DIRECTORY_SEPARATOR . 'layouts.xsd'
        );
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testLayouts($layout)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($layout));
        $errors = $this->_validateDomDocument($dom, $this->_schemaFile);
        $this->assertTrue(empty($errors), print_r($errors, true));
    }

    /**
     * @see self::testValidateLayouts
     * @return array
     * @throws Exception
     */
    public function validateDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles();
    }

    /**
     * @param DOMDocument $dom
     * @param $schemaFileName
     * @return array
     */
    protected function _validateDomDocument(DOMDocument $dom, $schemaFileName)
    {
        libxml_use_internal_errors(true);
        $result = $dom->schemaValidate($schemaFileName);
        $errors = array();
        if (!$result) {
            $validationErrors = libxml_get_errors();
            if (count($validationErrors)) {
                foreach ($validationErrors as $error) {
                    $errors[] = "{$error->message} Line: {$error->line}\n";
                }
            } else {
                $errors[] = 'Unknown validation error';
            }
        }
        libxml_use_internal_errors(false);
        return $errors;
    }
}
