<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class ChainLineBreak extends ConditionalLineBreak
{
    public function __construct()
    {
        parent::__construct(array(array(''), array('', new HardIndentLineBreak())));
    }
}
