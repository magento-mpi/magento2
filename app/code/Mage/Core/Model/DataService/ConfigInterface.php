<?php
/**
 * DataService config interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_DataService_ConfigInterface
{
    /**
     * @param $alias
     * @return mixed
     */
    public function getClassByAlias($alias);
}
