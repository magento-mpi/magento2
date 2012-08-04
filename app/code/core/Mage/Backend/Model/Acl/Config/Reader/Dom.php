<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Acl_Config_Reader_Dom extends Magento_Config_Dom
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
