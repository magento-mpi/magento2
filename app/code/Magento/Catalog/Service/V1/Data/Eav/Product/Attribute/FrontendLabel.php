<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Product\Attribute;

/**
 * Class FrontendLabel
 * @package Magento\Catalog\Service\V1\Data\Eav\Product\Attribute
 */
class FrontendLabel extends \Magento\Framework\Service\Data\AbstractObject
{
    /**
     * Constants used as keys into $_data
     */
    const STORE_ID = 'store_id';

    const LABEL = 'label';

    /**
     * Get store id value
     *
     * @return string
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }
}
