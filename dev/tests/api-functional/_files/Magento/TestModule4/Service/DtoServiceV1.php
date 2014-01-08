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
        $response = new Entity\V1\DtoResponseBuilder();
        return $response->setEntityId($id)->setName("Test")->create();
    }

    /**
     * {@inheritdoc}
     */
    public function updateData($id, Entity\V1\DtoRequest $request)
    {
        $response = new Entity\V1\DtoResponseBuilder();
        return $response->setEntityId($id)->setName($request->getName())->create();
    }

    /**
     * {@inheritdoc}
     */
    public function nestedData($id, Entity\V1\NestedDtoRequest $request)
    {
        $response = new Entity\V1\DtoResponseBuilder();
        return $response->setEntityId($id)->setName($request->getDetails()->getName())->create();
    }
}
