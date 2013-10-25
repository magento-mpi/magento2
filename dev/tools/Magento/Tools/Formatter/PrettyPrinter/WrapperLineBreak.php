<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class WrapperLineBreak
 * @package Magento\Tools\Formatter\PrettyPrinter
 *
 * Level 0:
 * if (x$a || $bx) {
 *
 * Level 1:
 * if (x
 *     $a || $bx
 * ) {
 */
class WrapperLineBreak extends ConditionalLineBreak
{
    public function __construct()
    {
        parent::__construct(array(array(''), array(new HardIndentLineBreak(), new HardLineBreak())));
    }
}