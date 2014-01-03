<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service;

class DtoServiceV1 implements \Magento\TestModule4\Service\DtoServiceV1Interface
{
    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $response = new Entity\V1\DtoResponse();
        $response->setEntityId($id);
        $response->setName("Test");
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateData($id, Entity\V1\DtoRequest $request)
    {
        $response = new Entity\V1\DtoResponse();
        $response->setEntityId($id);
        $response->setName($request->getName());
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function nestedData($id, Entity\V1\NestedDtoRequest $request)
    {
        $response = new Entity\V1\DtoResponse();
        $response->setEntityId($id);
        $response->setName($request->getDetails()->getName());
        return $response;
    }
}
