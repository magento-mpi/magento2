<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FormElement;

use Magento\Ui\DataType\DataTypeInterface;

/**
 * Class AbstractFormElement
 */
abstract class AbstractFormElement implements ElementInterface
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param DataTypeInterface $dataType
     * @return string
     */
    abstract public function render(DataTypeInterface $dataType);

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getFormInputName()
    {
        return $this->configuration['input_name'];
    }

    /**
     * @return bool
     */
    public function getIsReadonly()
    {
        return (bool) $this->configuration['readonly'];
    }

    /**
     * @return string
     */
    public function getCssClasses()
    {
        return $this->configuration['css_classes'];
    }
}
