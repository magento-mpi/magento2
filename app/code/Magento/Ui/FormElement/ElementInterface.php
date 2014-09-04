<?php
/**
 * {license}
 */

namespace Magento\Ui\FormElement;

use Magento\Ui\DataType\DataTypeInterface;

/**
 * Interface ElementInterface
 * @package Magento\Ui\FormElement
 */
interface ElementInterface
{
    /**
     * @param DataTypeInterface $dataType
     * @return string
     */
    public function render(DataTypeInterface $dataType);

    /**
     * @return string
     */
    public function getHtmlId();

    /**
     * @return string
     */
    public function getFormInputName();

    /**
     * @return bool
     */
    public function getIsReadonly();

    /**
     * @return string
     */
    public function getCssClasses();
}
