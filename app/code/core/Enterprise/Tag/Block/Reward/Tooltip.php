<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Advertising Tooltip block to show messages for gaining reward points when new tag submitted
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Block_Reward_Tooltip extends Enterprise_Reward_Block_Tooltip
{
    /**
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $data
        );

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }

        /** @var $helper Enterprise_Tag_Helper_Data */
        $helper = $this->_helper('Enterprise_Tag_Helper_Data');
        $helper->addActionClassToRewardModel();
    }


    /**
     * Helper getter
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _helper($helperName)
    {
        return isset($this->_helpers[$helperName]) ? $this->_helpers[$helperName] : Mage::helper($helperName);
    }
}
