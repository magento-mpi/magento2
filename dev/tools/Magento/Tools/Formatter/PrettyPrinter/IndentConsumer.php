<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This is a special class used to flag that a line does not get indented, no matter where it is in the hierarchy.
 * Class IndentConsumer
 */
class IndentConsumer
{
    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
