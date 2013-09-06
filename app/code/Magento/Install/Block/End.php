<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation ending block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Block_End extends Magento_Install_Block_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'end.phtml';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = (string) $this->_coreConfig->getNode('global/crypt/key');
            $this->setData('encryption_key', $key);
        }
        return $key;
    }

    /**
     * Return url for iframe source
     *
     * @return string|null
     */
    public function getIframeSourceUrl()
    {
        if (!Magento_AdminNotification_Model_Survey::isSurveyUrlValid()
            || Mage::getSingleton('Magento_Install_Model_Installer')->getHideIframe()) {
            return null;
        }
        return Magento_AdminNotification_Model_Survey::getSurveyUrl();
    }
}
