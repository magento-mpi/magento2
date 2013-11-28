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
     * @param int $id
     *
     * @return Entity\V1\DtoResponse
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
     * @param int $id
     * @param Entity\V1\DtoRequest $request
     * @return Entity\V1\DtoResponse
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
     * @param int $id
     * @param Entity\V1\NestedDtoRequest $request
     * @return Entity\V1\DtoResponse
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
