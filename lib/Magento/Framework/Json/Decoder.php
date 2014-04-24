<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Json;

/**
 * @package Magento\Framework\Json
 */
class Decoder implements DecoderInterface
{
    /**
     * Decodes the given $data string which is encoded in the JSON format.
     *
     * @param string $data
     * @return mixed
     */
    public function decode($data)
    {
        return \Zend_Json::decode($data);
    }
}
