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
 * History compaction strategy to compact file changes
 */
class Mage_DesignEditor_Model_History_Compact_Diff implements Mage_DesignEditor_Model_History_CompactInterface
{
    /**
     * Run compact strategy on given collection
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_Compact_Diff
     */
    public function compact($collection = null)
    {
        //@TODO compact strategies for file changes are not determined yet

        return $this;
    }

    /**
     * Set change collection
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_Compact_Diff
     */
    public function setChangesCollection(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        // TODO: Implement setChangesCollection() method.
        return $this;
    }

    /**
     * Get change collection
     *
     * @return Mage_DesignEditor_Model_Change_Collection
     */
    public function getChangesCollection()
    {
        // TODO: Implement getChangesCollection() method.
    }
}
