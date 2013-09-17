<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}Magento_Acl_Loader_Resource
 */
class Magento_Acl_Resource_Config_Dom extends Magento_Config_Dom
{
    /**
     * Return attribute for resource node that identify it as unique
     *
     * @param string $xPath
     * @return bool|string
     */
    protected function _findIdAttribute($xPath)
    {
        $needle = 'resource';
        return substr($xPath, -strlen($needle)) === $needle ? 'id' : false;
    }
}
