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
class OptionBuilder extends \Magento\Service\Data\AbstractObjectBuilder
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
}
