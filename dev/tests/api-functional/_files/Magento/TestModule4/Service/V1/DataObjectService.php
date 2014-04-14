<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1;

use Magento\TestModule4\Service\V1\Entity\DataObjectResponseBuilder;
use Magento\TestModule4\Service\V1\Entity\NestedDataObjectRequest;
use Magento\TestModule4\Service\V1\Entity\DataObjectRequest;

class DataObjectService implements \Magento\TestModule4\Service\V1\DataObjectServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $response = new DataObjectResponseBuilder();
        return $response->setEntityId($id)->setName("Test")->create();
    }

    /**
     * {@inheritdoc}
     */
    public function updateData($id, DataObjectRequest $request)
    {
        $response = new DataObjectResponseBuilder();
        return $response->setEntityId($id)->setName($request->getName())->create();
    }

    /**
     * {@inheritdoc}
     */
    public function nestedData($id, NestedDataObjectRequest $request)
    {
        $response = new DataObjectResponseBuilder();
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
