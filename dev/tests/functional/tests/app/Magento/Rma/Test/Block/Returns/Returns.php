<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns;

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Order view block
 *
 */
class Returns extends Block
{
    /**
     * Row selector
     *
     * @var string
     */
    protected $rowSelector = '//td[contains(text(), "%s")]';

    /**
     * Verify specific return.
     */
    public function isRowVisible($returnId)
    {
        return $this->_rootElement->find(sprintf($this->rowSelector, $returnId), Locator::SELECTOR_XPATH)->isVisible();
    }
}
