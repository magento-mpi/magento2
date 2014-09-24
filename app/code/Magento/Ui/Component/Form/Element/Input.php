<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Form\Element;

/**
 * Class Input
 */
class Input extends AbstractFormElement
{
    /**
     * @return mixed|string
     */
    public function getType()
    {
        return $this->getData('input_type') ? $this->getData('input_type') : 'text';
    }
}
