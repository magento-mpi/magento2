<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Eav;

class OptionBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Set option label
     *
     * @return OptionBuilder
     */
    public function setLabel($label)
    {
        return $this->_set(Option::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @return OptionBuilder
     */
    public function setValue($value)
    {
        return $this->_set(Option::VALUE, $value);
    }
}
