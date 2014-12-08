<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns;

use Magento\Rma\Test\Block\Returns\History\RmaRow;
use Magento\Rma\Test\Fixture\Rma;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Rma of order grid block.
 */
class History extends Block
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
            '\Magento\Rma\Test\Block\Returns\History\RmaRow',
            ['element' => $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)]
        );
    }

    // TODO: Remove function after refactoring functional Magento\Rma\Test\TestCase\RmaTest test
    /**
     * Get rma row by id.
     *
     * @param string $rmaId
     * @return RmaRow
     * @deprecated
     */
    public function getRmaRowById($rmaId)
    {
        $locator = sprintf($this->rmaRow, $rmaId);
        return $this->blockFactory->create(
            '\Magento\Rma\Test\Block\Returns\History\RmaRow',
            ['element' => $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)]
        );
    }
}
