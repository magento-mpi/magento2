<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Test\Utility;

class AggregateInvokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Test\Utility\AggregateInvoker
     */
    protected $_invoker;

    /**
     * @var \PHPUnit_Framework_TestCase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_testCase;

    protected function setUp()
    {
        $this->_testCase = $this->getMock(
            'PHPUnit_Framework_Test',
            ['run', 'count', 'fail', 'markTestIncomplete', 'markTestSkipped']
        );
        $this->_invoker = new AggregateInvoker($this->_testCase, array());
    }

    /**
     * @dataProvider callbackDataProvider
     *
     * @param string $expectedMessage
     * @param string $expectedMethod
     * @param string $exceptionClass
     * @throws
     */
    public function testMainFlow($expectedMessage, $expectedMethod, $exceptionClass)
    {
        $this->_testCase->expects(
            $this->any()
        )->method(
            $expectedMethod
        )->with(
            $this->stringStartsWith($expectedMessage)
        );
        $this->_invoker->__invoke(
            function () use ($exceptionClass) {
                throw new $exceptionClass('Some meaningful message.');
            },
            array(array(0))
        );
    }

    /**
     * @return array
     */
    public function callbackDataProvider()
    {
        return array(
            array(
                'Passed: 0, Failed: 1, Incomplete: 0, Skipped: 0.',
                'fail',
                'PHPUnit_Framework_AssertionFailedError'
            ),
            array('Passed: 0, Failed: 1, Incomplete: 0, Skipped: 0.', 'fail', 'PHPUnit_Framework_OutputError'),
            array(
                'Passed: 0, Failed: 1, Incomplete: 0, Skipped: 0.',
                'fail',
                'PHPUnit_Framework_ExpectationFailedException'
            ),
            array(
                'Passed: 0, Failed: 0, Incomplete: 1, Skipped: 0.',
                'markTestIncomplete',
                'PHPUnit_Framework_IncompleteTestError'
            ),
            array(
                'Passed: 0, Failed: 0, Incomplete: 0, Skipped: 1.',
                'markTestSkipped',
                'PHPUnit_Framework_SkippedTestError'
            )
        );
    }
}
