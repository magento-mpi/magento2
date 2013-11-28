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
     * @return Entity\V1\DtoResponse
     */
    public function getData($id);

    /**
     * @param int $id
     * @param Entity\V1\DtoRequest $request
     * @return Entity\V1\DtoResponse
     */
    public function updateData($id, Entity\V1\DtoRequest $request);


    /**
     * @param int $id
     * @param Entity\V1\NestedDtoRequest $request
     *
     * @return mixed
     */
    public function nestedData($id, Entity\V1\NestedDtoRequest $request);
}
