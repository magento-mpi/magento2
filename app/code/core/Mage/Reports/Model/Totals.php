<?php
/**
 *  Totals Class
 *
 * @package     Mage
 * @subpackage  Reports
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Reports_Model_Totals
{    
    public function countTotals($grid)
    {
        $columns = array();
        foreach ($grid->getColumns() as $col)
            $columns[$col->getIndex()] = array("total" => $col->getTotal(), "value" => 0);
                
        foreach ($grid->getCollection()->getItems() as $item)
        {        
            $data = $item->getData();
            foreach ($columns as $field=>$a)
                $columns[$field]['value'] += $data[$field];
        }
        $data = array();
        foreach ($columns as $field=>$a)
        {
            if ($a['total'] == 'avg')
            {
                $data[$field] = $a['value']/$grid->getCollection()->count();
            } else if ($a['total'] == 'sum')
                {
                    $data[$field] = $a['value'];
                } else if ($a['total'] != '') $data[$field] = $a['total'];
        }
        
        $totals = new Varien_Object();
        
        $totals->setData($data);
                                
        return $totals;
    }   
}
