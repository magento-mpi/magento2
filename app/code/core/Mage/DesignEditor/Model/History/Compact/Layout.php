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
        /** @var $change Mage_DesignEditor_Model_Change_LayoutAbstract */
        foreach ($collection as $changeKey => $change) {
            if (!$change instanceof Mage_DesignEditor_Model_Change_LayoutAbstract) {
                continue;
            }
            switch ($change->getData('action_name')) {
                case Mage_DesignEditor_Model_Change_Layout_Remove::LAYOUT_DIRECTIVE_REMOVE:
                    $this->_compactRemove($collection, $change, $changeKey);
                    break;

                case Mage_DesignEditor_Model_Change_Layout_Move::LAYOUT_DIRECTIVE_MOVE:
                    $this->_compactMove($collection, $change, $changeKey);
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
     * @param Mage_DesignEditor_Model_Change_LayoutAbstract $change
     * @param int $changeKey
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    protected function _compactRemove($collection, $change, $changeKey)
    {
        $target = $change->getData('element_name');
        /** @var $item Mage_DesignEditor_Model_Change_LayoutAbstract */
        foreach ($collection as $key => $item) {
            if (!$item instanceof Mage_DesignEditor_Model_Change_LayoutAbstract) {
                continue;
            }
            $isMoveOrRemove = in_array($item->getData('action_name'), array(
                Mage_DesignEditor_Model_Change_Layout_Remove::LAYOUT_DIRECTIVE_REMOVE,
                Mage_DesignEditor_Model_Change_Layout_Move::LAYOUT_DIRECTIVE_MOVE
            ));

            if ($item->getData('element_name') == $target && $isMoveOrRemove && $key != $changeKey) {
                $collection->removeItemByKey($key);
            }
        }

        return $this;
    }

    /**
     * Run compact for move action
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @param Mage_DesignEditor_Model_Change_LayoutAbstract $change
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    protected function _compactMove($collection, $change)
    {
        $originContainer = $change->getData('origin_container');
        $originOrder = $change->getData('origin_order');

        $target = $change->getData('element_name');
        $lastMove = null;
        $lastKey = null;
        /** @var $item Mage_DesignEditor_Model_Change_LayoutAbstract */
        foreach ($collection as $key => $item) {
            if (!$item instanceof Mage_DesignEditor_Model_Change_Layout_Move ||
                    $item->getData('element_name') != $target) {
                continue;
            }

            if ($lastMove) {
                $collection->removeItemByKey($lastKey);
            }
            $lastMove = $item;
            $lastKey = $key;
        }

        if ($lastMove) {
            $hasContainerChanged = $lastMove->getData('destination_container') != $originContainer;
            if (!$hasContainerChanged) {
                $hasOrderChanged = $lastMove->getData('destination_order') != $originOrder;
                if (!$hasOrderChanged) {
                    $collection->removeItemByKey($lastKey);
                }
            }
        }

        return $this;
    }
}
