<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Order view block
 *
 * @package Magento\Rma\Test\Block
 */
class MyReturns extends Block
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
    public function assertReturn($returnId)
    {
        $return = $this->_rootElement->find(sprintf($this->rowSelector, $returnId), Locator::SELECTOR_XPATH);
        if (!$return->isVisible()) {
            throw new \Exception(sprintf('"%s" return is not visible', $returnId));
        }
    }
}
