<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Json;

/**
 * @package Magento\Json
 */
class Decoder implements DecoderInterface
{
    /**
     * @param string $data
     * @return mixed
     */
    public function decode($data)
    {
        return \Zend_Json::decode($data);
    }
}