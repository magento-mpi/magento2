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
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = (string) Mage::getConfig()->getNode('global/crypt/key');
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
