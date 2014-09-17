<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FormElement;

use Magento\Ui\DataType\DataTypeInterface;
use Magento\Ui\ViewInterface;

/**
 * Interface ElementInterface
 */
interface ElementInterface extends ViewInterface
{
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
