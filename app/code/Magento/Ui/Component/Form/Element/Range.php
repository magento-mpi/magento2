<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Form\Element;

/**
 * Class Range
 */
class Range extends AbstractFormElement
{
    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getData('input_type');
    }
}
