<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FormElement;

/**
 * Class Input
 */
class Input extends AbstractFormElement
{
    public function getType()
    {
        return $this->getData('input_type') ? $this->getData('input_type') : 'text';
    }
}
