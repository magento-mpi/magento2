<?php
/**
 * Data service visitable interface
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Dataservice_Path_Visitable
{
    /**
     * @param Mage_Core_Model_Dataservice_Path_Visitor $visitor
     * @return mixed
     */
    public function visit(Mage_Core_Model_Dataservice_Path_Visitor $visitor);
}