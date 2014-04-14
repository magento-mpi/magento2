<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model;

/**
 *  Totals Class
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals
{
    /**
     * Retrieve count totals
     *
     * @param \Magento\Backend\Block\Widget\Grid $grid
     * @param string $from
     * @param string $to
     * @return \Magento\Object
     */
    public function countTotals($grid, $from, $to)
    {
        $columns = array();
        foreach ($grid->getColumns() as $col) {
            $columns[$col->getIndex()] = array("total" => $col->getTotal(), "value" => 0);
        }

        $count = 0;
        /**
         * This method doesn't work because of commit 6e15235, see MAGETWO-4751
         */
        $report = $grid->getCollection()->getReportFull($from, $to);
        foreach ($report as $item) {
            if ($grid->getSubReportSize() && $count >= $grid->getSubReportSize()) {
                continue;
            }
            $data = $item->getData();

            foreach ($columns as $field => $a) {
                if ($field !== '') {
                    $columns[$field]['value'] = $columns[$field]['value'] + (isset($data[$field]) ? $data[$field] : 0);
                }
            }
            $count++;
        }
        $data = array();
        foreach ($columns as $field => $a) {
            if ($a['total'] == 'avg') {
                if ($field !== '') {
                    if ($count != 0) {
                        $data[$field] = $a['value'] / $count;
                    } else {
                        $data[$field] = 0;
                    }
                }
            } elseif ($a['total'] == 'sum') {
                if ($field !== '') {
                    $data[$field] = $a['value'];
                }
            } elseif (strpos($a['total'], '/') !== false) {
                if ($field !== '') {
                    $data[$field] = 0;
                }
            }
        }

        $totals = new \Magento\Object();
        $totals->setData($data);

        return $totals;
    }
}
