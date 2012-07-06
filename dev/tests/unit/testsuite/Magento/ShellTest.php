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
    /**
     * @dataProvider executeDataProvider
     * @param string $phpCommand
     * @param bool $isVerbose
     * @param string $expectedResult
     * @param string $expectedOutput
     */
    public function testExecute($phpCommand, $isVerbose = false, $expectedResult = '', $expectedOutput = '')
    {
        $this->expectOutputString($expectedOutput);
        $shell = new Magento_Shell($isVerbose);
        $actualOutput = $shell->execute('php -r %s', array($phpCommand));
        $this->assertEquals($expectedResult, $actualOutput);
    }

    public function executeDataProvider()
    {
        return array(
            'capture STDOUT' => array('echo "test_output";',           false, 'test_output'),
            'print STDOUT'   => array('echo "test_output";',           true,  'test_output', 'test_output' . PHP_EOL),
            'capture STDERR' => array('fwrite(STDERR, "test_error");', false, 'test_error'),
            'print STDERR'   => array('fwrite(STDERR, "test_error");', true,  'test_error',  'test_error' . PHP_EOL),
        );
    }

    /**
     * @expectedException Magento_Shell_Exception
     * @expectedExceptionMessage Command `non_existing_command` finished with non-zero exit code
     */
    public function testExecuteNonExistingCommand()
    {
        $shell = new Magento_Shell();
        $shell->execute('non_existing_command');
    }

    /**
     * @expectedException Magento_Shell_Exception
     * @expectedExceptionCode 42
     */
    public function testExecuteNonZeroExitCode()
    {
        $this->testExecute('exit(42);');
    }
}
