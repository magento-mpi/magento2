<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_SalesArchive_Model_System_Config_Backend_Active
    extends Magento_Backend_Model_Config_Backend_Cache
    implements Magento_Backend_Model_Config_CommentInterface
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        Magento_Backend_Block_Menu::CACHE_TAGS
    );

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged() && !$this->getValue()) {
            Mage::getModel('Magento_SalesArchive_Model_Archive')->removeOrdersFromArchive();
        }
        return $this;
    }

    /**
     * Get field comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getCommentText($currentValue)
    {
        if ($currentValue) {
            $ordersCount = Mage::getResourceSingleton('Magento_SalesArchive_Model_Resource_Order_Collection')
                ->getSize();
            if ($ordersCount) {
                return __('There are %1 orders in this archive. All of them will be moved to the regular table after the archive is disabled.', $ordersCount);
            }
        }
        return '';
    }
}
