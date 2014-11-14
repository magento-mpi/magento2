<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class Option
 *
 * @codeCoverageIgnore
 */
class Option extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';

    const VALUE = 'value';

    const ORDER = 'order';

    const STORE_LABELS = 'store_labels';

    const IS_DEFAULT = 'default';

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
     * Get option value
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * Get option order
     *
     * @return int|null
     */
    public function getOrder()
    {
        return $this->_get(self::ORDER);
    }

    /**
     * is default
     *
     * @return bool|null
     */
    public function isDefault()
    {
        return $this->_get(self::IS_DEFAULT);
    }

    /**
     * Set option label for store scopes
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option\Label[]|null
     */
    public function getStoreLabels()
    {
        return $this->_get(Option::STORE_LABELS);
    }
}
