<?php
/**
 * Dataservice config interface.
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Dataservice_Config_Interface
{
    /**
     * @param $alias
     * @return mixed
     */
    public function getClassByAlias($alias);
}
