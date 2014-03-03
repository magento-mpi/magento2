<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1;

use Magento\TestModule4\Service\V1\Entity\NestedDataObjectRequest;
use Magento\TestModule4\Service\V1\Entity\DataObjectRequest;

interface DataObjectServiceInterface
{
    /**
     * @param int $id
     * @return \Magento\TestModule4\Service\V1\Entity\DataObjectResponse
     */
    public function getData($id);

    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\V1\Entity\DataObjectRequest $request
     * @return \Magento\TestModule4\Service\V1\Entity\DataObjectResponse
     */
    public function updateData($id, DataObjectRequest $request);


    /**
     * @param int $id
     * @param \Magento\TestModule4\Service\V1\Entity\NestedDataObjectRequest $request
     * @return \Magento\TestModule4\Service\V1\Entity\DataObjectResponse
     */
    public function nestedData($id, NestedDataObjectRequest $request);

    /**
     * Test return scalar value
     *
     * @param int $id
     * @return int
     */
    public function scalarResponse($id);
}
