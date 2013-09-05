<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}\Magento\Acl\Loader\Resource
 */
namespace Magento\Acl\Resource\Config;

class Dom extends \Magento\Config\Dom
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
