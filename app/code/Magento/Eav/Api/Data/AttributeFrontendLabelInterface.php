<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data;

interface AttributeFrontendLabelInterface 
{
    const LABEL = 'label';

    const STORE_ID = 'store_id';

    /**
     * Return label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Return store id
     *
     * @return int
     */
    public function getStoreId();
}
