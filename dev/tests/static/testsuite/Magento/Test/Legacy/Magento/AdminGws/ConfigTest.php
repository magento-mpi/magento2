<?php
/**
 * AdminGWS configuration nodes validator
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Test\Legacy\Magento\AdminGws;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testEventSubscriberFormat()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             */
            function ($file) {
                $xml = simplexml_load_file($file);
                $nodes = $xml->xpath(\Magento\Test\Integrity\Magento\AdminGws\ConfigTest::CLASSES_XPATH) ?: [];
                $errors = [];
                /** @var SimpleXMLElement $node */
                foreach ($nodes as $node) {
                    if (preg_match('/\_\_/', $node->getName())) {
                        $errors[] = $node->getName();
                    }
                }
                if ($errors) {
                    $this->fail("Obsolete class names detected in {$file}:\n" . implode(PHP_EOL, $errors) . PHP_EOL);
                }
            },
            \Magento\Framework\Test\Utility\Files::init()->getMainConfigFiles()
        );
    }
}
