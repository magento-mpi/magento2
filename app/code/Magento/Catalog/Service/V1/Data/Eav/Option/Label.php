<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Option;

/**
 * Class Store Scope Label
 *
 * @codeCoverageIgnore
 */
class Label extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';
    const STORE_ID = 'store_id';

    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }
}
