<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Matcher;

class MethodInvokedAtIndex implements \PHPUnit_Framework_MockObject_Matcher_Invocation
{
    /**
     * @var integer
     */
    protected $sequenceIndex;

    /**
     * @var integer
     */
    protected $currentIndex = -1;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @param integer $sequenceIndex
     * @param string $methodName
     */
    public function __construct($sequenceIndex, $methodName)
    {
        $this->sequenceIndex = $sequenceIndex;
        $this->methodName = $methodName;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'invoked at sequence index ' . $this->sequenceIndex;
    }

    /**
     * @param  \PHPUnit_Framework_MockObject_Invocation $invocation
     * @return boolean
     */
    public function matches(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if ($this->methodName == $invocation->methodName) {
            $this->currentIndex++;
        }
        return $this->currentIndex == $this->sequenceIndex;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation $invocation
     * @returns null
     */
    public function invoked(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        return;
    }

    /**
     * Verifies that the current expectation is valid. If everything is OK the
     * code should just return, if not it must throw an exception.
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @returns null
     */
    public function verify()
    {
        if ($this->currentIndex >= $this->sequenceIndex) {
            return;
        }
        throw new \PHPUnit_Framework_ExpectationFailedException(
            sprintf(
                'The expected invocation at index %s was never reached.',
                $this->sequenceIndex
            )
        );
    }
} 