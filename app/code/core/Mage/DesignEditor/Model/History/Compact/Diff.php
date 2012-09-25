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
     * @return Mage_DesignEditor_Model_History_Compact_Layout|Mage_DesignEditor_Model_History_CompactInterface
     */
    public function compact(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        //@TODO compact strategies for file changes are not determined yet

        return $this;
    }
}
