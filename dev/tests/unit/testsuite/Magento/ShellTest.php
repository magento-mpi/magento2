<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ShellTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetVerbose()
    {
        $shell = new Magento_Shell(false);
        $this->assertFalse($shell->getVerbose());

        $shell->setVerbose(true);
        $this->assertTrue($shell->getVerbose());

        $shell->setVerbose(false);
        $this->assertFalse($shell->getVerbose());
    }

    /**
     * @param string $phpCommand
     * @param string $expectedRaw
     * @param string $expectedFull
     * @dataProvider executeDataProvider
     */
    public function testExecute($phpCommand, $expectedRaw, $expectedFull)
    {
        // non-verbose
        $shell = new Magento_Shell();
        $rawResult = $shell->execute('php -r %s', array($phpCommand), $fullResult);
        $this->assertEquals($expectedRaw, $rawResult);
        $this->assertEquals($expectedFull, $fullResult);

        // verbose
        $this->expectOutputString($expectedFull);
        $shell = new Magento_Shell(true);
        $shell->execute('php -r %s', array($phpCommand));
    }

    public function executeDataProvider()
    {
        $quote = escapeshellarg('\'""');
        $quote = $quote[0];
        return array(
            'STDOUT' => array('echo 27182;', array('27182'),
                "php -r {$quote}echo 27182;{$quote} 2>&1" . PHP_EOL . '27182' . PHP_EOL . PHP_EOL
            ),
            'STDERR' => array('fwrite(STDERR, 27183);', array('27183'),
                "php -r {$quote}fwrite(STDERR, 27183);{$quote} 2>&1" . PHP_EOL . '27183' . PHP_EOL . PHP_EOL
            ),
        );
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Command `non_existing_command` returned non-zero exit code
     * @expectedExceptionCode 0
     */
    public function testExecuteFailure()
    {
        $shell = new Magento_Shell();
        $shell->execute('non_existing_command');
    }

    /**
     * @param string $phpCommand
     * @param string $expectedRaw
     * @param string $expectedFull
     * @dataProvider executeDataProvider
     */
    public function testExecuteFailureDetails($phpCommand, $expectedRaw, $expectedFull)
    {
        try {
            /* Force command to return non-zero exit code */
            $this->testExecute($phpCommand . ' exit(42);', $expectedRaw, $expectedFull);
        } catch (Magento_Exception $e) {
            $this->assertInstanceOf('Exception', $e->getPrevious());
            $this->assertEquals(implode(PHP_EOL, $expectedRaw), $e->getPrevious()->getMessage());
            $this->assertEquals(42, $e->getPrevious()->getCode());
        }
    }
}
