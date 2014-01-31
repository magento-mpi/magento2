<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1;

use Magento\TestModule4\Service\V1\Entity\NestedDtoRequest;
use Magento\TestModule4\Service\V1\Entity\DtoRequest;

interface DtoServiceInterface
{
    /**
     * @param int $id
     * @return \Magento\TestModule4\Service\V1\Entity\DtoResponse
     */
    public function getData($id);

    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\V1\Entity\DtoRequest $request
     * @return \Magento\TestModule4\Service\V1\Entity\DtoResponse
     */
    public function updateData($id, DtoRequest $request);


    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\V1\Entity\NestedDtoRequest $request
     * @return \Magento\TestModule4\Service\V1\Entity\DtoResponse
     */
    public function nestedData($id, NestedDtoRequest $request);

    /**
     * Test return scalar value
     *
     * @param int $id
     * @return int
     */
    public function scalarResponse($id);
}
