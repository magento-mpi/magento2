<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_Resource_ConfigReader_Xml_Dom extends Magento_Config_Dom
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
