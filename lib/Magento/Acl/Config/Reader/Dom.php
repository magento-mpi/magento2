<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  ACL
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Magento
 * @package     Framework
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Acl_Config_Reader_Dom extends Magento_Config_Dom
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
