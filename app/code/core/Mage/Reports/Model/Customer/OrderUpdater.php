<?php
class Mage_Reports_Model_Customer_OrderUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{

    /**
     * Update specified argument
     *
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        $argument->setReportCollection('Mage_Reports_Model_Resource_Customer_Orders_Collection');
        return $argument;
    }
}