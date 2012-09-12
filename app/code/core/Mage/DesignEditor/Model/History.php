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
 * Visual Design Editor history model
 */
class Mage_DesignEditor_Model_History
{
    /**
     * Base class for all change instances
     */
    const BASE_CHANGE_CLASS = 'Mage_DesignEditor_Model_ChangeAbstract';

    /**
     * Changes collection class
     */
    const CHANGE_COLLECTION_CLASS = 'Mage_DesignEditor_Model_Change_Collection';

    /**
     * Internal collection of changes
     *
     * @var Mage_DesignEditor_Model_Change_Collection
     */
    protected $_collection;

    /**
     * Initialize empty internal collection
     */
    public function __construct()
    {
        $this->_initCollection();
    }

    /**
     * Initialize changes collection
     *
     * @return Mage_DesignEditor_Model_History
     */
    protected function _initCollection()
    {
        $this->_collection = Mage::getModel(self::CHANGE_COLLECTION_CLASS);
        return $this;
    }

    /**
     * Get change instance
     *
     * @param string $type
     * @param array $data
     * @return Mage_DesignEditor_Model_ChangeAbstract
     */
    protected function _getChangeItem($type, $data)
    {
        $item = Mage_DesignEditor_Model_Change_Factory::getInstance($type);
        $item->setData($data);

        return $item;
    }

    /**
     * Get change type
     *
     * @param mixed $change
     * @throws Exception
     * @return string
     */
    protected function _getChangeType($change)
    {
        $type = null;
        if (is_array($change)) {
            $type = isset($change['type']) ? $change['type'] : null;
        } elseif ($change instanceof Varien_Object) {
            $type = $change->getType();
        }

        if (!$type) {
            throw new Exception('Impossible to get change type');
        }

        return $type;
    }

    /**
     * Load changes from DB. To be able to effectively compact changes they should be all loaded first.
     *
     * @return Mage_DesignEditor_Model_History
     */
    public function loadChanges()
    {
        return $this;
    }

    /**
     * Add change to internal collection
     *
     * @param mixed $item
     * @param array|null $data
     * @return Mage_DesignEditor_Model_History
     */
    public function addChange($item, $data = null)
    {
        $baseChangeClass = self::BASE_CHANGE_CLASS;
        if (!$item instanceof $baseChangeClass) {
            $type = $item;
            $item = $this->_getChangeItem($type, $data);
        }
        $this->_collection->addItem($item);

        return $this;
    }

    /**
     * Add changes to internal collection
     *
     * @param Traversable $changes
     * @return Mage_DesignEditor_Model_History
     */
    public function addChanges(Traversable $changes)
    {
        foreach ($changes as $change) {
            $type = $this->_getChangeType($change);
            $this->addChange($type, $change);
        }

        return $this;
    }

    /**
     *  Set changes to internal collection
     *
     * @param Traversable $changes
     * @return Mage_DesignEditor_Model_History
     */
    public function setChanges(Traversable $changes)
    {
        $changesCollectionClass = self::CHANGE_COLLECTION_CLASS;
        if ($changes instanceof $changesCollectionClass) {
            $this->_collection = $changes;
        } else {
            $this->_initCollection();
            foreach ($changes as $change) {
                $type = $this->_getChangeType($change);
                $this->addChange($type, $change);
            }
        }

        return $this;
    }

    /**
     * Get changes collection
     *
     * @return Mage_DesignEditor_Model_Change_Collection
     */
    public function getChanges()
    {
        return $this->_collection;
    }
}
