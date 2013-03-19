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
 * History compaction model
 */
class Mage_DesignEditor_Model_History_Compact
{
    /**
     * Configuration for compact model
     *
     * @var array
     */
    protected $_config = array('Mage_DesignEditor_Model_History_Compact_Layout');

    /**
     * Storage of compact strategies
     *
     * @var array
     */
    protected $_compactModels = array();

    /**
     * Compact collection of changes
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @throws Mage_Core_Exception
     * @return Mage_DesignEditor_Model_History_Compact
     */
    public function compact(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        $itemType = $collection->getItemClass();
        if (!$itemType == 'Mage_DesignEditor_Model_ChangeAbstract') {
            Mage::throwException(
                Mage::helper('Mage_DesignEditor_Helper_Data')->__('Invalid collection items\' type "%s"', $itemType)
            );
        }

        /** @var $model Mage_DesignEditor_Model_History_CompactInterface */
        foreach ($this->_getCompactModels() as $model) {
            $model->compact($collection);
        }

        return $this;
    }

    /**
     * Get compaction strategies array ordered to minimize performance impact
     *
     * @return array
     */
    protected function _getCompactModels()
    {
        if (!$this->_compactModels) {
            foreach ($this->_getConfig() as $class) {
                $this->_compactModels[] = Mage::getModel($class);
            }
        }

        return $this->_compactModels;
    }

    /**
     * Get configuration for compact
     *
     * @return array
     */
    protected function _getConfig()
    {
        return $this->_config;
    }
}
