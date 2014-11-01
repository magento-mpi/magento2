<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class OptionBuilder
 *
 * @codeCoverageIgnore
 */
class OptionBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * Set option label
     *
     * @param  string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(Option::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @param  string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(Option::VALUE, $value);
    }

    /**
     * Get option label
     *
     * @param int $value
     * @return $this
     */
    public function setOrder($value)
    {
        return $this->_set(Option::ORDER, $value);
    }

    /**
     * Get option order
     *
     * @param bool $value
     * @return $this
     */
    public function setDefault($value)
    {
        return $this->_set(Option::IS_DEFAULT, $value);
    }

    /**
     * Set option label for store scope
     *
     * @param  \Magento\Catalog\Service\V1\Data\Eav\Option\Label[] $value
     * @return $this
     */
    public function setStoreLabels($value)
    {
        return $this->_set(Option::STORE_LABELS, $value);
    }
}
