<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class HardIndentLineBreak extends HardLineBreak
{
    /**
     * This method returns if the next line should be indented.
     */
    public function isNextLineIndented()
    {
        return true;
    }
}