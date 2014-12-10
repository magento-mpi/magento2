<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\View\Tab;

/**
 * Invitation view status history tab block
 *
 */
class History extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'view/tab/history.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Invitation History Factory
     *
     * @var \Magento\Invitation\Model\Invitation\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Invitation\Model\Invitation\HistoryFactory $historyFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Invitation\Model\Invitation\HistoryFactory $historyFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_historyFactory = $historyFactory;
    }

    /**
     * Returns the Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Status History');
    }

    /**
     * Returns the Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Status History');
    }

    /**
     * Return whether the tab can be shown
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Return whether the tab is hidden
     *
     * @return false
     */
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
        return $this->_coreRegistry->registry('current_invitation');
    }

    /**
     * Return invitation status history collection
     *
     * @return \Magento\Invitation\Model\Resource\Invitation\History\Collection
     */
    public function getHistoryCollection()
    {
        return $this->_historyFactory->create()->getCollection()->addFieldToFilter(
            'invitation_id',
            $this->getInvitation()->getId()
        )->addOrder(
            'history_id'
        );
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
            $date = $this->_localeDate->date($date, \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT);
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
            $date = $this->_localeDate->date($date, \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT);
        }

        return parent::formatTime($date, $format, $showDate);
    }
}
