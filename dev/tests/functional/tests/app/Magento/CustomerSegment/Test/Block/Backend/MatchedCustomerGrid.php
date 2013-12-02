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
    const GRID_XPATH = '//table[@id="segmentGrid_table"]/tbody/tr';
    const GRID_NAME_COL = '/td[2]';
    const GRID_EMAIL_COL = '/td[3]';
    const GRID_GROUP_COL = '/td[4]';
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'email' => array(
                'selector' => '#segmentGrid_filter_grid_email'
            )
        );
    }

    /**
     * Get Name text from matched customer grid
     *
     * @return string
     */
    public function getGridName()
    {
        return $this->_rootElement->find(self::GRID_XPATH.self::GRID_NAME_COL,
            Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Email text from matched customer grid
     *
     * @return string
     */
    public function getGridEmail()
    {
        return $this->_rootElement->find(self::GRID_XPATH.self::GRID_EMAIL_COL,
            Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Group text from matched customer grid
     *
     * @return string
     */
    public function getGridGroup()
    {
        return $this->_rootElement->find(self::GRID_XPATH.self::GRID_GROUP_COL,
            Locator::SELECTOR_XPATH)->getText();
    }
}