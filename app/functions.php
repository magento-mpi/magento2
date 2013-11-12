<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Create value-object \Magento\Phrase
 *
 * @return string
 */
function __()
{
    $argc = func_get_args();

    /**
     * Type casting to string is a workaround.
     * Many places in client code at the moment are unable to handle the \Magento\Phrase object properly.
     * The intended behavior is to use __toString(),
     * so that rendering of the phrase happens only at the last moment when needed
     */
    return (string)new \Magento\Phrase(array_shift($argc), $argc);
}
