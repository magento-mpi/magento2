<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1;

use Magento\TestModule4\Service\V1\Entity\DtoResponseBuilder;
use Magento\TestModule4\Service\V1\Entity\NestedDtoRequest;
use Magento\TestModule4\Service\V1\Entity\DtoRequest;

class DtoService implements \Magento\TestModule4\Service\V1\DtoServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $response = new DtoResponseBuilder();
        return $response->setEntityId($id)->setName("Test")->create();
    }

    /**
     * {@inheritdoc}
     */
    public function updateData($id, DtoRequest $request)
    {
        $response = new DtoResponseBuilder();
        return $response->setEntityId($id)->setName($request->getName())->create();
    }

    /**
     * {@inheritdoc}
     */
    public function nestedData($id, NestedDtoRequest $request)
    {
        $response = new DtoResponseBuilder();
        return $response->setEntityId($id)->setName($request->getDetails()->getName())->create();
    }

    /**
     * Test return scalar value
     *
     * @param int $id
     * @return int
     */
    public function scalarResponse($id)
    {
        return $id;
    }
}
