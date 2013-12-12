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

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Report\Customer\Segment\Detail;

use Mtf\Client\Element\Locator;

/**
 * Class MatchedCustomerGrid
 * Backend segment matched customer grid
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Report\Customer\Segment\Detail
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * XPath for segment grid row
     */
    const GRID_XPATH_ROW = '//table[@id="segmentGrid_table"]/tbody/tr';
    /**
     * The Xpath column for the Name
     */
    const GRID_NAME_COL = '/td[2]';
    /**
     * The Xpath column for the Email
     */
    const GRID_EMAIL_COL = '/td[3]';
    /**
     * The Xpath column for the Group
     */
    const GRID_GROUP_COL = '/td[4]';
    /**
     * Initialize block elements
     */

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'email' => array(
            'selector' => '#segmentGrid_filter_grid_email'
        )
    );

    /**
     * Get Name text from matched customer grid
     *
     * @return string
     */
    public function getGridName()
    {
        return $this->_rootElement->find(self::GRID_XPATH_ROW.self::GRID_NAME_COL, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Email text from matched customer grid
     *
     * @return string
     */
    public function getGridEmail()
    {
        return $this->_rootElement->find(self::GRID_XPATH_ROW.self::GRID_EMAIL_COL, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Group text from matched customer grid
     *
     * @return string
     */
    public function getGridGroup()
    {
        return $this->_rootElement->find(self::GRID_XPATH_ROW.self::GRID_GROUP_COL, Locator::SELECTOR_XPATH)->getText();
    }
}