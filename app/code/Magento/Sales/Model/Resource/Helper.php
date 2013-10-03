<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Mysql resource helper model
 */
namespace Magento\Sales\Model\Resource;

class Helper extends \Magento\Core\Model\Resource\Helper
    implements \Magento\Sales\Model\Resource\HelperInterface
{
    /**
     * @var \Magento\Reports\Model\Resource\Helper
     */
    protected $_reportsResourceHelper;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Reports\Model\Resource\Helper $reportsResourceHelper
     * @param string $modulePrefix
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Reports\Model\Resource\Helper $reportsResourceHelper,
        $modulePrefix = 'sales'
    ) {
        parent::__construct($resource, $modulePrefix);
        $this->_reportsResourceHelper = $reportsResourceHelper;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of \Magento\Sales\Model\Resource\Report\Bestsellers::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return \Magento\Sales\Model\Resource\Helper
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationAliases,
        $mainTable, $aggregationTable
    ) {
        if ($aggregation == $aggregationAliases['monthly']) {
            $this->_reportsResourceHelper->updateReportRatingPos('month', 'qty_ordered', $mainTable, $aggregationTable);
        } elseif ($aggregation == $aggregationAliases['yearly']) {
            $this->_reportsResourceHelper->updateReportRatingPos('year', 'qty_ordered', $mainTable, $aggregationTable);
        } else {
            $this->_reportsResourceHelper->updateReportRatingPos('day', 'qty_ordered', $mainTable, $aggregationTable);
        }

        return $this;
    }
}
