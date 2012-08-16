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
 * Visual design editor manager model
 */
class Mage_DesignEditor_Model_History_Manager extends Mage_Core_Model_Abstract
{
    /**
     * Change collection
     *
     * @var null|Mage_DesignEditor_Model_History_Manager_Collection
     */
    protected $_changeCollection;

    /**
     * Add change
     *
     * @param array $change
     * @return Mage_DesignEditor_Model_History_Manager
     */
    public function addChange($change)
    {
        $this->_getChangeCollection()->addElement($change);
        return $this;
    }

    /**
     * Get history log
     *
     * @return array
     */
    public function getHistoryLog()
    {
        return $this->_getChangeCollection()->toHistoryLog();
    }

    /**
     * Get xml changes
     *
     * @return string
     */
    public function getXml()
    {
        return $this->_getChangeCollection()->toXml();
    }

    /**
     * Get change collection
     *
     * @return Mage_DesignEditor_Model_History_Manager_Collection
     */
    protected function _getChangeCollection()
    {
        if($this->_changeCollection == null) {
            $this->_changeCollection = Mage::getModel('Mage_DesignEditor_Model_History_Manager_Collection');
        }
        return $this->_changeCollection;
    }
}
