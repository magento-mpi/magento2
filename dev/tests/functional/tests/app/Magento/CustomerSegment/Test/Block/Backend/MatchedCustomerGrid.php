<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class MatchedCustomerGrid
 * Backend segment matched customer grid
 *
 * @package Magento\CustomerSegment\Test\Block\Backend
 */
class MatchedCustomerGrid extends Grid {
    /**
     * The first row in grid.
     *
     * @var string
     */
    protected $colEmail;

    /**
     * Retrieve the email in the first row
     */
    public function setGridEmail()
    {
        $this->rowItem = $this->_rootElement->find('//table[@id="segmentGrid_table"]/tbody/tr/td[3]',
            Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * getter for the email in the first row
     */
    public function getGridEmail()
    {
        return $this->colEmail;
    }
}