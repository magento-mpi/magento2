<?php
/**
 * Data source visitable interface
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Datasource_Path_Visitable
{
    /**
     * @param Mage_Core_Model_Datasource_Path_Visitor $visitor
     * @return mixed
     */
    public function visit(Mage_Core_Model_Datasource_Path_Visitor $visitor);
}