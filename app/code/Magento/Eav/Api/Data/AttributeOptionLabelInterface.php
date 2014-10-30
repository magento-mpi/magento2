<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeOptionLabelInterface
{
    const LABEL = 'label';

    const STORE_ID = 'store_id';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();
}
