<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shell;

class CommandRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $testArgument  = 'argument';
        $testArgument2 = 'argument2';
        $commandRenderer = new CommandRenderer();
        $this->assertEquals(
            "php -r " . escapeshellarg($testArgument) . " 2>&1 | grep " . escapeshellarg($testArgument2) . " 2>&1",
            $commandRenderer->render('php -r %s | grep %s', array($testArgument, $testArgument2))
        );
    }
}
