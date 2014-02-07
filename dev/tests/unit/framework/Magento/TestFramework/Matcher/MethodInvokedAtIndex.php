<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Matcher;

class MethodInvokedAtIndex extends \PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex
{
    /**
     * @var array
     */
    protected $indexes = array();

    /**
     * @param  \PHPUnit_Framework_MockObject_Invocation $invocation
     * @return boolean
     */
    public function matches(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if (!isset($this->indexes[$invocation->methodName])) {
            $this->indexes[$invocation->methodName] = 0;
        } else {
            $this->indexes[$invocation->methodName]++;
        }
        $this->currentIndex++;

        return $this->indexes[$invocation->methodName] == $this->sequenceIndex;
    }

} 