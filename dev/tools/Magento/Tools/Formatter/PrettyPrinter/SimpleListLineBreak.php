<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
