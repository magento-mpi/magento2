<?php
/**
 * Used to get class information associated with an alias, and stored in config files.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_DataService_ConfigInterface
{
    /**
     * Get the class information for a given service call
     *
     * @param $alias
     * @return mixed
     */
    public function getClassByAlias($alias);
}
