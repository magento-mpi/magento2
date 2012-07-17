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
     * @param string $expectedOutput
     * @param string $expectedResult
     */
    public function testExecute($phpCommand, $isVerbose, $expectedOutput, $expectedResult = '')
    {
        $this->expectOutputString($expectedOutput);
        $shell = new Magento_Shell($isVerbose);
        $actualResult = $shell->execute('php -r %s', array($phpCommand));
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function executeDataProvider()
    {
        return array(
            'capture STDOUT' => array('echo 27182;',            false, '',                '27182'),
            'print STDOUT'   => array('echo 27182;',            true,  '27182' . PHP_EOL, '27182'),
            'capture STDERR' => array('fwrite(STDERR, 27182);', false, '',                '27182'),
            'print STDERR'   => array('fwrite(STDERR, 27182);', true,  '27182' . PHP_EOL, '27182'),
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
     * @dataProvider executeDataProvider
     * @param string $phpCommand
     * @param bool $isVerbose
     * @param string $expectedOutput
     * @param string $expectedError
     */
    public function testExecuteFailureDetails($phpCommand, $isVerbose, $expectedOutput, $expectedError = '')
    {
        try {
            /* Force command to return non-zero exit code */
            $this->testExecute($phpCommand . ' exit(42);', $isVerbose, $expectedOutput);
        } catch (Magento_Exception $e) {
            $this->assertInstanceOf('Exception', $e->getPrevious());
            $this->assertEquals($expectedError, $e->getPrevious()->getMessage());
            $this->assertEquals(42, $e->getPrevious()->getCode());
        }
    }
}
