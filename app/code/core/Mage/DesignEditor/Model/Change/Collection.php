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
 * Visual design editor changes collection
 */
class Mage_DesignEditor_Model_Change_Collection extends Varien_Data_Collection
{
    /**
     * Collection item class
     *
     * @var string
     */
    protected $_itemObjectClass = 'Mage_DesignEditor_Model_ChangeAbstract';

    /**
     * Get collection item class
     *
     * @return string
     */
    public function getItemClass()
    {
        return $this->_itemObjectClass;
    }

    /**
     * Get array of changes suited for encoding to JSON
     *
     * @param array $fields
     * @return array
     */
    public function toArray($fields = array())
    {
        $items = array();
        /** @var $item Mage_DesignEditor_Model_ChangeAbstract */
        foreach ($this as $item) {
            $items[] = $item->toArray($fields);
        }
        return $items;
    }
}
