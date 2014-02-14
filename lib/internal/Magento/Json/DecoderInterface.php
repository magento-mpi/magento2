<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Json;

/**
 * Json decoder
 *
 * @package Magento\Json
 */
interface DecoderInterface
{
    /**
     * Decodes the given $data string which is encoded in the JSON format.
     *
     * @param string $data
     * @return mixed
     */
    public function decode($data);
}
