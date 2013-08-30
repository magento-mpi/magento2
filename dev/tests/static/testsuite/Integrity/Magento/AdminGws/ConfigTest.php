<?php
/**
 * AdminGWS configuration nodes validator
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Magento_AdminGws_ConfigTest extends PHPUnit_Framework_TestCase
{
    const CLASSES_XPATH
        = '/config/adminhtml/magento/admingws/*[name()!="controller_predispatch" and name()!="acl_deny"]/*';

    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testEventSubscriberFormat($file)
    {
        $xml = simplexml_load_file($file);
        $nodes = $xml->xpath(Integrity_Magento_AdminGws_ConfigTest::CLASSES_XPATH) ?: array();
        $errors = array();
        /** @var SimpleXMLElement $node */
        foreach ($nodes as $node) {
            $class = implode('_', array_map('ucfirst', explode('_', $node->getName())));
            if (!Utility_Files::init()->classFileExists($class, $path)) {
                $errors[] = "'{$node->getName()}' => '{$path}'";
            }
        }
        if ($errors) {
            $this->fail("Invalid class declarations in {$file}. Files are not found in code pools:\n"
                . implode(PHP_EOL, $errors) . PHP_EOL
            );
        }
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('config.xml');
    }
}
