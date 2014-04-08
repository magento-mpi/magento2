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
    /**
     * Covered CommandRenderer class
     *
     * @test
     */
    public function testRender()
    {
        $testArgument = 'argument';
        $commandRenderer = new CommandRenderer();
        $this->assertEquals(
            "php -r '$testArgument' 2>&1",
            $commandRenderer->render('php -r %s', array($testArgument))
        );
    }
}
