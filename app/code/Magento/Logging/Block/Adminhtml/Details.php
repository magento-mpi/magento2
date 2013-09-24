<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log grid container
 */
class Magento_Logging_Block_Adminhtml_Details extends Magento_Backend_Block_Widget_Container
{
    /**
     * Store curent event
     *
     * @var Magento_Logging_Model_Event
     */
    protected $_currentEevent = null;

    /**
     * Store current event user
     *
     * @var Magento_User_Model_User
     */
    protected $_eventUser = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * User model
     *
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_User_Model_UserFactory $userFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_User_Model_UserFactory $userFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_coreRegistry = $registry;
        $this->_userFactory = $userFactory;
    }

    /**
     * Add back button
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_addButton('back', array(
            'label'   => __('Back'),
            'onclick' => "setLocation('" . Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/*/'). "')",
            'class'   => 'back'
        ));
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCurrentEvent()) {
            return __('Log Entry #%1', $this->getCurrentEvent()->getId());
        }
        return __('Log Entry Details');
    }

    /**
     * Get current event
     *
     * @return Magento_Logging_Model_Event|null
     */
    public function getCurrentEvent()
    {
        if (null === $this->_currentEevent) {
            $this->_currentEevent = $this->_coreRegistry->registry('current_event');
        }
        return $this->_currentEevent;
    }

    /**
     * Convert x_forwarded_ip to string
     *
     * @return string|bool
     */
    public function getEventXForwardedIp()
    {
        if ($this->getCurrentEvent()) {
            $xForwarderFor = long2ip($this->getCurrentEvent()->getXForwardedIp());
            if ($xForwarderFor && $xForwarderFor != '0.0.0.0') {
                return $xForwarderFor;
            }
        }
        return false;
    }

    /**
     * Convert ip to string
     *
     * @return string|bool
     */
    public function getEventIp()
    {
        if ($this->getCurrentEvent()) {
            return long2ip($this->getCurrentEvent()->getIp());
        }
        return false;
    }

    /**
     * Replace /n => <br /> in event error_message
     *
     * @return string|bool
     */
    public function getEventError()
    {
        if ($this->getCurrentEvent()) {
            return nl2br($this->getCurrentEvent()->getErrorMessage());
        }
        return false;
    }

    /**
     * Get current event user
     *
     * @return Magento_User_Model_User|null
     */
    public function getEventUser()
    {
        if (null === $this->_eventUser) {
            $this->_eventUser = $this->_userFactory->create()->load($this->getUserId());
        }
        return $this->_eventUser;
    }

    /**
     * Unserialize and retrive event info
     *
     * @return string
     */
    public function getEventInfo()
    {
        $info = null;
        $data = $this->getCurrentEvent()->getInfo();
        try {
            $info = unserialize($data);
        } catch (Exception $e) {
            $info = $data;
        }
        return $info;
    }
}
