<?php
/**
 * Find "backend/system.xml" files and validate them
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Backend;

class SystemConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $configFile
             */
            function ($configFile) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($configFile));
                $schema = \Magento\TestFramework\Utility\Files::init()->getPathToSource() .
                    '/app/code/Magento/Backend/etc/system_file.xsd';
                $errors = \Magento\Config\Dom::validateDomDocument($dom, $schema);
                if ($errors) {
                    $this->fail('XML-file has validation errors:' . PHP_EOL . implode(PHP_EOL . PHP_EOL, $errors));
                }
            },
            \Magento\TestFramework\Utility\Files::init()->getConfigFiles('adminhtml/system.xml', array())
        );
    }
}
