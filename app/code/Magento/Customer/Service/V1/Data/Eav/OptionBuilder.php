<?php
/**
 * Eav attribute option
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

class OptionBuilder extends \Magento\Service\Entity\AbstractObjectBuilder
{
    /**
     * Set option label
     *
     * @param $label
     * @return OptionBuilder
     */
    public function setLabel($label)
    {
        return $this->_set(Option::LABEL, $label);
    }

    /**
     * Set option value
     *
     * @param $value
     * @return OptionBuilder
     */
    public function setValue($value)
    {
        return $this->_set(Option::VALUE, $value);
    }
}
