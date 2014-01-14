<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service;

interface DtoServiceV1Interface
{
    /**
     * @param int $id
     * @return \Magento\TestModule4\Service\Entity\V1\DtoResponse
     */
    public function getData($id);

    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\Entity\V1\DtoRequest $request
     * @return \Magento\TestModule4\Service\Entity\V1\DtoResponse
     */
    public function updateData($id, Entity\V1\DtoRequest $request);


    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\Entity\V1\NestedDtoRequest $request
     * @return \Magento\TestModule4\Service\Entity\V1\DtoResponse
     */
    public function nestedData($id, Entity\V1\NestedDtoRequest $request);

    /**
     * Test return scalar value
     *
     * @param int $id
     * @return int
     */
    public function scalarResponse($id);
}
