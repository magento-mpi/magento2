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
     * @var Magento_AdminNotification_Model_Survey
     */
    protected $_survey;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_AdminNotification_Model_Survey $survey,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_coreConfig = $coreConfig;
        $this->_survey = $survey;
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
        if (!$this->_survey->isSurveyUrlValid()
            || Mage::getSingleton('Magento_Install_Model_Installer')->getHideIframe()) {
            return null;
        }
        return $this->_survey->getSurveyUrl();
    }
}
