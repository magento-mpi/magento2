<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_SalesArchive_Model_System_Config_Backend_Active
    extends Mage_Adminhtml_Model_System_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        Mage_Backend_Block_Menu::CACHE_TAGS
    );

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged() && !$this->getValue()) {
            Mage::getModel('Enterprise_SalesArchive_Model_Archive')->removeOrdersFromArchive();
        }
        return $this;
    }

    /**
     * Get field comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getCommentText($element, $currentValue)
    {
        if ($currentValue) {
            $ordersCount = Mage::getResourceSingleton('Enterprise_SalesArchive_Model_Resource_Order_Collection')
                ->getSize();
            if ($ordersCount) {
                return Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('There are %s orders in archive. All of them will be moved to regular table after archive is disabled.', $ordersCount);
            }
        }
        return '';
    }
}
