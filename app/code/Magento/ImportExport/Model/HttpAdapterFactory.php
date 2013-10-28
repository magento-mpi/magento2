<?php
/**
 * Created by PhpStorm.
 * User: okorshenko
 * Date: 28.10.13
 * Time: 17:00
 */

namespace Magento\ImportExport\Model;

class HttpAdapterFactory
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