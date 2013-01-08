<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block before edit form
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit_Before extends Mage_Backend_Block_Template
{
    /**
     * Core helper
     *
     * @var Mage_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * Basic import model
     *
     * @var Mage_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Core_Helper_Data $coreHelper
     * @param Mage_ImportExport_Model_Import $importModel
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct( Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Mage_Core_Helper_Data $coreHelper,
        Mage_ImportExport_Model_Import $importModel,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $data
        );
        $this->_coreHelper = $coreHelper;
        $this->_importModel = $importModel;
    }

    /**
     * Returns json-encoded entity behaviors array
     *
     * @return string
     */
    public function getEntityBehaviors()
    {
        $importModel = $this->_importModel;
        $behaviors = $importModel::getEntityBehaviors();
        foreach ($behaviors as $entityCode => $behavior) {
            $behaviors[$entityCode] = $behavior['code'];
        }
        return $this->_coreHelper->jsonEncode($behaviors);
    }

    /**
     * Return json-encoded list of existing behaviors
     *
     * @return string
     */
    public function getUniqueBehaviors()
    {
        $importModel = $this->_importModel;
        $uniqueBehaviors = $importModel::getUniqueEntityBehaviors();
        return $this->_coreHelper->jsonEncode(array_keys($uniqueBehaviors));
    }
}
