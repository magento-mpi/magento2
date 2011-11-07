<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Php_CodeMessTest extends PHPUnit_Framework_TestCase
{

    public function testRulesetFormat()
    {
        $ruleset = __DIR__ . '/_files/phpmd/ruleset.xml';
        $rulesetAvailable = file_exists($ruleset) && is_readable($ruleset);
        $this->assertTrue($rulesetAvailable, 'PHP Mess Detector rule set file is not available.');

        $schemaFile = __DIR__ . '/_files/phpmd/ruleset_xml_schema.xsd';

        $doc = new DOMDocument();
        $doc->load($ruleset);

        libxml_use_internal_errors(true);
        $result = $doc->schemaValidate($schemaFile);
        if ($result === false) {
            $result = "XML-file is invalid.\n";
            foreach (libxml_get_errors() as $error) {
                /* @var libXMLError $error */
                $result .= "{$error->message} File: {$error->file} Line: {$error->line}\n";
            }
        }
        libxml_use_internal_errors(false);
        $this->assertTrue($result, $result);
    }
}
