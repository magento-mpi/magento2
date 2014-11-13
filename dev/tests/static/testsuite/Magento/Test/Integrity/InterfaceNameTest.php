<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity;

use Magento\TestFramework\Utility\AggregateInvoker;
use Magento\TestFramework\Utility\Files;

class InterfaceNameTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceNameEndsWithInterfaceSuffix()
    {
        $invoker = new AggregateInvoker($this);
        $invoker(
            /**
             * @param string $path
             */
            function ($path) {
                preg_match_all('/^interface ([A-Za-z0-9_]+)/m', file_get_contents($path), $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $interfaceName) {
                        $this->assertStringEndsWith(
                            'Interface',
                            $interfaceName,
                            sprintf(
                                "Interface %s declared in %s should have name that ends with 'Interface' suffix.",
                                $interfaceName,
                                $path
                            )
                        );
                    }
                }
            },
            Files::init()->getPhpFiles()
        );
    }
}
