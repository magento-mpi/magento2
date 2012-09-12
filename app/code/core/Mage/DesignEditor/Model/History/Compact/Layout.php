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
 * History compaction strategy to compact layout changes
 */
class Mage_DesignEditor_Model_History_Compact_Layout implements Mage_DesignEditor_Model_History_CompactInterface
{
    /**
     * Run compact strategy on given collection
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_Compact_Layout|Mage_DesignEditor_Model_History_CompactInterface
     */
    public function compact(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        /** @var $change Mage_DesignEditor_Model_Change_Layout */
        foreach ($collection as $change) {
            if (!$change instanceof Mage_DesignEditor_Model_Change_Layout) {
                continue;
            }
            switch ($change->getData('action_name')) {
                case Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_REMOVE:
                    $this->_compactRemove($collection, $change);
                    break;

                case Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_MOVE:
                    $this->_compactMove($collection, $change);
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Run compact for remove action
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @param Mage_DesignEditor_Model_Change_Layout $change
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    protected function _compactRemove($collection, $change)
    {
        $target = $change->getData('element_name');
        /** @var $item Mage_DesignEditor_Model_Change_Layout */
        foreach ($collection as $item) {
            $isMoveOrRemove = in_array($item->getData('action_name'), array(
                Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_REMOVE,
                Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_MOVE
            ));
            if ($item->getData('element_name') == $target && $isMoveOrRemove && $item->getId() != $change->getId()) {
                $collection->removeItemByKey($item->getId());
            }
        }

        return $this;
    }

    /**
     * Run compact for move action
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @param Mage_DesignEditor_Model_Change_Layout $change
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    protected function _compactMove($collection, $change)
    {
        $target = $change->getData('element_name');
        $lastMove = null;
        /** @var $item Mage_DesignEditor_Model_Change_Layout */
        foreach ($collection as $item) {
            if ($item->getData('element_name') != $target || $item->getData('action_name')) {
                continue;
            }

            if ($lastMove) {
                $collection->removeItemByKey($lastMove->getId());
            }
            $lastMove = $item;
        }

        return $this;
    }
}
