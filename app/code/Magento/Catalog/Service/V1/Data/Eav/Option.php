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
 */
class Option extends \Magento\Framework\Service\Data\AbstractObject
{
    /**
     * Constants used as keys into $_data
     */
    const LABEL = 'label';

    const VALUE = 'value';

    const ORDER = 'order';

    const STORE_LABELS = 'store_labels';

    const IS_DEFAULT = 'is_default';

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
     * @return bool
     */
    public function isDefault()
    {
        return $this->_get(self::IS_DEFAULT);
    }

    /**
     * Set option label for store scopes
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option\Label[]
     */
    public function getStoreLabels()
    {
        return $this->_get(Option::STORE_LABELS);
    }
}
