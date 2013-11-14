<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

interface LineCondition
{
    /**
     * This method checks the current condition for the next token being added to the line and
     * determines if the current token should be removed.
     * @param string $nextToken String containing the next token being added to the line.
     */
    public function removeToken($nextToken);
}
