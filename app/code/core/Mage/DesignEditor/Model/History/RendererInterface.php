<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * History output renderer interface
 */
interface Mage_DesignEditor_Model_History_RendererInterface
{
    /**
     * Signature of compact method to implement in subclasses
     *
     * @abstract
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return string
     */
    public function render(Mage_DesignEditor_Model_Change_Collection $collection);
}
