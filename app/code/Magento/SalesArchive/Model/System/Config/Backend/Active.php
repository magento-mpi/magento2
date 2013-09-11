<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\System\Config\Backend;

class Active
    extends \Magento\Backend\Model\Config\Backend\Cache
    implements \Magento\Backend\Model\Config\CommentInterface
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        \Magento\Backend\Block\Menu::CACHE_TAGS
    );

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged() && !$this->getValue()) {
            \Mage::getModel('Magento\SalesArchive\Model\Archive')->removeOrdersFromArchive();
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
            $ordersCount = \Mage::getResourceSingleton('Magento\SalesArchive\Model\Resource\Order\Collection')
                ->getSize();
            if ($ordersCount) {
                return __('There are %1 orders in this archive. All of them will be moved to the regular table after the archive is disabled.', $ordersCount);
            }
        }
        return '';
    }
}
