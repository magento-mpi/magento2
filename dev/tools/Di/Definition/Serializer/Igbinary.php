<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Definition\Serializer;

class Igbinary implements SerializerInterface
{
    public function __construct()
    {
        if (!function_exists('igbinary_serialize')) {
            throw new \LogicException('Igbinary extension not loaded');
        }
    }

    /**
     * Serialize input data
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return igbinary_serialize($data);
    }
}
