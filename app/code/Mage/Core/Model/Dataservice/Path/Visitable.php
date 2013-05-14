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
     * @param Mage_Core_Model_Dataservice_Path_Navigator $visitor
     * @return mixed
     */
    public function accept(Mage_Core_Model_Dataservice_Path_Navigator $visitor);
}