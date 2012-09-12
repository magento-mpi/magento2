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
 * History compaction strategies interface
 */
interface Mage_DesignEditor_Model_History_CompactInterface
{
    /**
     * Signature of compact method to implement in subclasses
     *
     * @abstract
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_CompactInterface
     */
    public function compact(Mage_DesignEditor_Model_Change_Collection $collection);
}
