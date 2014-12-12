<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class WrapperLineBreak
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
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct([[''], ['', new HardLineBreak()]]);
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        return 100;
    }
}
