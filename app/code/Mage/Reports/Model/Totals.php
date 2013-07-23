<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Totals Class
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Totals
{
    /**
     * Retrieve count totals
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $grid
     * @param string $from
     * @param string $to
     * @return Varien_Object
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

            foreach ($columns as $field=>$a) {
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
                        $data[$field] = $a['value']/$count;
                    } else {
                        $data[$field] = 0;
                    }
                }
            } else if ($a['total'] == 'sum') {
                if ($field !== '') {
                    $data[$field] = $a['value'];
                }
            } else if (strpos($a['total'], '/') !== FALSE) {
                if ($field !== '') {
                    $data[$field] = 0;
                }
            }
        }

        $totals = new Varien_Object();
        $totals->setData($data);

        return $totals;
    }
}
