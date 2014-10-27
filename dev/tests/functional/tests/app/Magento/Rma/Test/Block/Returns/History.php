<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\Block\Returns;

use Mtf\Block\Block;
use \Magento\Rma\Test\Block\Returns\History\RmaTable;

/**
 * Rma of order grid block.
 */
class History extends Block
{
    /**
     * Locator for rma table.
     *
     * @var string
     */
    protected $rmaTable = '.returns';

    /**
     * Return rma table.
     *
     * @return RmaTable
     */
    public function getRmaTable()
    {
        return $this->blockFactory->create(
            '\Magento\Rma\Test\Block\Returns\History\RmaTable',
            ['element' => $this->_rootElement->find($this->rmaTable)]
        );
    }
}
