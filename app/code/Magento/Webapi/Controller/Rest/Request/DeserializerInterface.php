<?php
/**
 * Interface of REST request content deserializer.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request\Rest;

interface DeserializerInterface
{
    /**
     * Parse request body into array of params.
     *
     * @param string $body Posted content from request
     * @return array|null Return NULL if content is invalid
     */
    public function deserialize($body);
}
