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
     * Test that PHP command returns an expected result
     *
     * @param Magento_Shell $shell
     * @param string $phpCommand
     * @param string $expectedResult
     */
    protected function _testExecutePhpCommand(Magento_Shell $shell, $phpCommand, $expectedResult)
    {
        $this->expectOutputString(''); // nothing is expected to be ever printed to the standard output
        $actualResult = $shell->execute('php -r %s', array($phpCommand));
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string $phpCommand
     * @param string $expectedResult
     * @dataProvider executeDataProvider
     */
    public function testExecute($phpCommand, $expectedResult)
    {
        $this->_testExecutePhpCommand(new Magento_Shell(), $phpCommand, $expectedResult);
    }

    /**
     * @param string $phpCommand
     * @param string $expectedResult
     * @param array $expectedLogRecords
     * @dataProvider executeDataProvider
     */
    public function testExecuteLog($phpCommand, $expectedResult, $expectedLogRecords)
    {
        $logger = $this->getMock('Zend_Log', array('log'));
        foreach ($expectedLogRecords as $logRecordIndex => $expectedLogMessage) {
            $logger
                ->expects($this->at($logRecordIndex))
                ->method('log')
                ->with($expectedLogMessage, Zend_Log::INFO)
            ;
        }
        $this->_testExecutePhpCommand(new Magento_Shell($logger), $phpCommand, $expectedResult);
    }

    public function executeDataProvider()
    {
        $quote = substr(escapeshellarg(' '), 0, 1);
        return array(
            'STDOUT' => array(
                'echo 27181;', '27181', array("php -r {$quote}echo 27181;{$quote} 2>&1", '27181'),
            ),
            'STDERR' => array(
                'fwrite(STDERR, 27182);', '27182', array("php -r {$quote}fwrite(STDERR, 27182);{$quote} 2>&1", '27182'),
            ),
        );
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Command `non_existing_command 2>&1` returned non-zero exit code
     * @expectedExceptionCode 0
     */
    public function testExecuteFailure()
    {
        $shell = new Magento_Shell();
        $shell->execute('non_existing_command');
    }

    /**
     * @param string $phpCommand
     * @param string $expectedError
     * @dataProvider executeDataProvider
     */
    public function testExecuteFailureDetails($phpCommand, $expectedError)
    {
        try {
            /* Force command to return non-zero exit code */
            $phpFailingCommand = $phpCommand . ' exit(42);';
            $this->testExecute($phpFailingCommand, ''); // no result is expected in a case of a command failure
        } catch (Magento_Exception $e) {
            $this->assertInstanceOf('Exception', $e->getPrevious());
            $this->assertEquals($expectedError, $e->getPrevious()->getMessage());
            $this->assertEquals(42, $e->getPrevious()->getCode());
        }
    }
}
