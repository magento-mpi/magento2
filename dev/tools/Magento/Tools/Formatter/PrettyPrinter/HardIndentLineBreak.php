<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class HardIndentLineBreak extends HardLineBreak
{
    /**
     * This method returns if the next line should be indented.
     *
     * @return bool
     */
    public function isNextLineIndented()
    {
        return true;
    }
}
