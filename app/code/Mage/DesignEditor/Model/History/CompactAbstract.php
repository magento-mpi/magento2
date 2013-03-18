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
 * Compaction model abstract
 */
abstract class Mage_DesignEditor_Model_History_CompactAbstract
    implements Mage_DesignEditor_Model_History_CompactInterface
{
    /**
     * Changes collection
     *
     * @var Mage_DesignEditor_Model_Change_Collection
     */
    protected $_changesCollection;

    /**
     * Set change collection
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return Mage_DesignEditor_Model_History_Compact_Layout
     */
    public function setChangesCollection(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        $this->_changesCollection = $collection;
        return $this;
    }

    /**
     * Get change collection
     *
     * @return Mage_DesignEditor_Model_Change_Collection
     */
    public function getChangesCollection()
    {
        return $this->_changesCollection;
    }

    /**
     * Signature of compact method to implement in subclasses
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @throws Magento_Exception
     * @return Mage_DesignEditor_Model_History_CompactInterface
     */
    public function compact($collection = null)
    {
        if (null === $collection) {
            if (!$this->getChangesCollection()) {
                throw new Magento_Exception('Compact collection is missed');
            }
        }
        return $this->setChangesCollection($collection);
    }
}
