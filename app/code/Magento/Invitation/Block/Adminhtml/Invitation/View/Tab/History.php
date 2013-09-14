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
class Magento_Invitation_Block_Adminhtml_Invitation_View_Tab_History
    extends Magento_Adminhtml_Block_Template
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_template = 'view/tab/history.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

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
     * @return Magento_Invitation_Model_Invitation
     */
    public function getInvitation()
    {
        return $this->_coreRegistry->registry('current_invitation');
    }

    /**
     * Return invitation status history collection
     *
     * @return Magento_Invitation_Model_Resource_Invitation_History_Collection
     */
    public function getHistoryCollection()
    {
        return Mage::getModel('Magento_Invitation_Model_Invitation_History')
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
    public function formatDate($date = null, $format = 'short', $showTime = false)
    {
        if (is_string($date)) {
            $date = Mage::app()->getLocale()->date($date, Magento_Date::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatDate($date, $format, $showTime);
    }

    /**
     * Retrieve formatting time
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime($date = null, $format = 'short', $showDate = false)
    {
        if (is_string($date)) {
            $date = Mage::app()->getLocale()->date($date, Magento_Date::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatTime($date, $format, $showDate);
    }
}
