<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\FormElement;

use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Interface ElementInterface
 */
interface ElementInterface extends UiComponentInterface
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
