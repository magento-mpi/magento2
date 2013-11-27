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
        if ($id == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }
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
        if ($id == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }
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
        if ($id == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }
        $response = new Entity\V1\DtoResponse();
        $response->setEntityId($id);
        $response->setName($request->getDetails()->getName());
        return $response;
    }
}
