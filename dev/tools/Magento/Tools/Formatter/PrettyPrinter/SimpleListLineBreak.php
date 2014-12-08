<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class SimpleListLineBreak extends ConditionalLineBreak
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct([['', ' '], ['', new HardIndentLineBreak()]]);
    }
}
