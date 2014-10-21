<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/***
 * Class Returns
 * Order view block/
 */
class Returns extends History
{
    /**
     * Row selector.
     *
     * @var string
     */
    protected $rowSelector = '//td[contains(text(), "%s")]';

    /**
     * Verify specific return.
     *
     * @param int $returnId
     * @return bool
     */
    public function isRowVisible($returnId)
    {
        return $this->_rootElement->find(sprintf($this->rowSelector, $returnId), Locator::SELECTOR_XPATH)->isVisible();
    }
}
