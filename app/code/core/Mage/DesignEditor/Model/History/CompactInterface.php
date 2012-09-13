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
     * Set change collection
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    public function setChangesCollection(Mage_DesignEditor_Model_Change_Collection $collection);

    /**
     * Get change collection
     *
     * @return Mage_DesignEditor_Model_Change_Collection
     */
    public function getChangesCollection();

    /**
     * Signature of compact method to implement in subclasses
     *
     * @param Mage_DesignEditor_Model_Change_Collection|null $collection
     * @throws Magento_Exception
     * @return Mage_DesignEditor_Model_History_CompactInterface
     */
    public function compact($collection = null);
}
