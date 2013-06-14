<?php
/**
 * ACL config file list
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Acl_Loader_Resource_ConfigReader_FileListInterface
{
    /**
     * Retrieve list of configuration files
     *
     * @return array
     */
    public function asArray();
}
