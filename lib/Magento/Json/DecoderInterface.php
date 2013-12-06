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
     * @param string $data
     * @return mixed
     */
    public function decode($data);
}
