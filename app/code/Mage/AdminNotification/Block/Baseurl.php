<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Adminhtml_Baseurl extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Config_DataFactory
     */
    protected $_configDataFactory;

    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param Mage_Core_Model_Config_DataFactory $configDataFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_StoreManager $storeManager,
        Mage_Core_Model_Config_DataFactory $configDataFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_configDataFactory = $configDataFactory;
    }

    /**
     * Get url for config settings where base url option can be changed
     *
     * @return string|bool
     */
    public function getConfigUrl()
    {
        $defaultUnsecure = (string) $this->_config
            ->getNode('default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);

        $defaultSecure = (string) $this->_config
            ->getNode('default/' . Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);

        if ($defaultSecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
            || $defaultUnsecure == Mage_Core_Model_Store::BASE_URL_PLACEHOLDER
        ) {
            return $this->getUrl('adminhtml/system_config/edit', array('section'=>'web'));
        }

        $configData = $this->_configDataFactory->create();
        $dataCollection = $configData->getCollection()
            ->addValueFilter(Mage_Core_Model_Store::BASE_URL_PLACEHOLDER);

        $url = false;
        foreach ($dataCollection as $data) {
            if ($data->getScope() == 'stores') {
                $code = $this->_storeManager->getStore($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', array('section'=>'web', 'store'=>$code));
            }
            if ($data->getScope() == 'websites') {
                $code = $this->_storeManager->getWebsite($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', array('section'=>'web', 'website'=>$code));
            }

            if ($url) {
                return $url;
            }
        }
        return $url;
    }
}
