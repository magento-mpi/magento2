<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Form\Element;

/**
 * Class Select
 */
class Select extends AbstractFormElement
{
    /**
     * Check if option value
     *
     * @param $optionValue
     * @return bool
     */
    public function getIsSelected($optionValue)
    {
        return $this->getValue() == $optionValue;
    }
}
