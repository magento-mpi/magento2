<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP\Adapter;

class FileTransferFactory
{
    /**
     * Create HTTP adapter
     *
     * @param array $options
     * @return \Zend_File_Transfer_Adapter_Http
     */
    public function create(array $options = array())
    {
        return new \Zend_File_Transfer_Adapter_Http($options);
    }
} 