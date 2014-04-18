<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Create value-object \Magento\Framework\Phrase
 *
 * @return string
 */
function __()
{
    $argc = func_get_args();

    /**
     * Type casting to string is a workaround.
     * Many places in client code at the moment are unable to handle the \Magento\Framework\Phrase object properly.
     * The intended behavior is to use __toString(),
     * so that rendering of the phrase happens only at the last moment when needed
     */
    return (string)new \Magento\Framework\Phrase(array_shift($argc), $argc);
}
