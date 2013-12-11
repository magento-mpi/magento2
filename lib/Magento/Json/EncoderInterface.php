<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Json;

/**
 * Json encoder
 *
 * @package Magento\Json
 */
interface EncoderInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data);
}
