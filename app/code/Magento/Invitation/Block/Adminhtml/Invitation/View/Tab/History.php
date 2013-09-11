<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation view status history tab block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\View\Tab;

class History
    extends \Magento\Adminhtml\Block\Template
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    protected $_template = 'view/tab/history.phtml';

    public function getTabLabel()
    {
        return __('Status History');
    }
    public function getTabTitle()
    {
        return __('Status History');
    }

    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }

    /**
     * Return Invitation for view
     *
     * @return \Magento\Invitation\Model\Invitation
     */
    public function getInvitation()
    {
        return \Mage::registry('current_invitation');
    }

    /**
     * Return invintation status history collection
     *
     * @return Magento_Invintation_Model_Resource_Invintation_History_Collection
     */
    public function getHistoryCollection()
    {
        return \Mage::getModel('Magento\Invitation\Model\Invitation\History')
            ->getCollection()
            ->addFieldToFilter('invitation_id', $this->getInvitation()->getId())
            ->addOrder('history_id');
    }

    /**
     * Retrieve formating date
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatDate($date=null, $format='short', $showTime=false)
    {
        if (is_string($date)) {
            $date = \Mage::app()->getLocale()->date($date, \Magento\Date::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatDate($date, $format, $showTime);
    }

    /**
     * Retrieve formating time
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime($date=null, $format='short', $showDate=false)
    {
        if (is_string($date)) {
            $date = \Mage::app()->getLocale()->date($date, \Magento\Date::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatTime($date, $format, $showDate);
    }
}
