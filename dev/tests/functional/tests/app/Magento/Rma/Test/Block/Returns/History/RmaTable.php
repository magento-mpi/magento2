<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns\History;

use Mtf\Client\Element\Locator;
use Mtf\Block\Block;
use Magento\Rma\Test\Block\Returns\History\RmaTable\RmaRow;
use Magento\Rma\Test\Fixture\Rma;

/**
 * Class RmaTable
 * Rma table.
 */
class RmaTable extends Block
{
    /**
     * Selector for rma row.
     *
     * @var string
     */
    protected $rmaRow = './/tbody/tr[./*[contains(@class,"col id") and normalize-space(.)="%s"]]';

    /**
     * Get rma row.
     *
     * @param Rma $rma
     * @return RmaRow
     */
    public function getRmaRow(Rma $rma)
    {
        $locator = sprintf($this->rmaRow, $rma->getEntityId());
        return $this->blockFactory->create(
            '\Magento\Rma\Test\Block\Returns\History\RmaTable\RmaRow',
            ['element' => $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)]
        );
    }
}
