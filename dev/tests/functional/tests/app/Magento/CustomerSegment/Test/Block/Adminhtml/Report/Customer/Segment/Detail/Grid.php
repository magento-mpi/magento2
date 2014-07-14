<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\Detail;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;

/**
 * Class MatchedCustomerGrid
 * Backend segment matched customer grid
 */
class Grid extends ParentGrid
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
     * The total records path
     */
    const TOTAL_RECORDS = '.pages-total-found';

    /**
     * Filters array mapping
     *
     * @var array
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
        return $this->_rootElement->find(self::GRID_XPATH_ROW . self::GRID_NAME_COL, Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Get Email text from matched customer grid
     *
     * @return string
     */
    public function getGridEmail()
    {
        return $this->_rootElement->find(self::GRID_XPATH_ROW . self::GRID_EMAIL_COL, Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Get Group text from matched customer grid
     *
     * @return string
     */
    public function getGridGroup()
    {
        return $this->_rootElement->find(self::GRID_XPATH_ROW . self::GRID_GROUP_COL, Locator::SELECTOR_XPATH)
            ->getText();
    }

    /**
     * Get total records in grid
     *
     * @return int
     */
    public function getTotalRecords()
    {
        $totalRecordsText = $this->_rootElement->find(self::TOTAL_RECORDS, Locator::SELECTOR_CSS)->getText();
        preg_match('`Total (\d*?) `', $totalRecordsText, $total);
        return (int) $total[1];
    }
}
