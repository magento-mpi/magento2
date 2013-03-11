<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Definition\Serializer;


class Standard implements SerializerInterface
{
    /**
     * Serialize input data
     *
     * @param mixed $data
     * @return string
     */
    public function serialzie($data)
    {
        return serialize($data);
    }

}
