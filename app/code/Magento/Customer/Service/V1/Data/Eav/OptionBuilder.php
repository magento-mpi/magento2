<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

/**
 * Class OptionBuilder
 */
class OptionBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Set option label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(Option::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(Option::VALUE, $value);
    }


    /**
     * Set nested options
     *
     * @param \Magento\Customer\Service\V1\Data\Eav\Option[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this->_set(Option::OPTIONS, $options);
    }
}
