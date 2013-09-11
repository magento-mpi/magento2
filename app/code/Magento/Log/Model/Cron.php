<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Cron Model
 *
 * @category   Magento
 * @package    Magento_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Log\Model;

class Cron extends \Magento\Core\Model\AbstractModel
{
    const XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE     = 'system/log/error_email_template';
    const XML_PATH_EMAIL_LOG_CLEAN_IDENTITY     = 'system/log/error_email_identity';
    const XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT    = 'system/log/error_email';
    const XML_PATH_LOG_CLEAN_ENABLED            = 'system/log/enabled';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Send Log Clean Warnings
     *
     * @return \Magento\Log\Model\Cron
     */
    protected function _sendLogCleanEmail()
    {
        if (!$this->_errors) {
            return $this;
        }
        if (!\Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT)) {
            return $this;
        }

        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        /* @var $translate \Magento\Core\Model\Translate */
        $translate->setTranslateInline(false);

        $emailTemplate = \Mage::getModel('Magento\Core\Model\Email\Template');
        /* @var $emailTemplate \Magento\Core\Model\Email\Template */
        $emailTemplate->setDesignConfig(array('area' => 'backend', 'store' => \Mage::app()->getStore()->getId()))
            ->sendTransactional(
                \Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE),
                \Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY),
                \Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT),
                null,
                array('warnings' => join("\n", $this->_errors))
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Clean logs
     *
     * @return \Magento\Log\Model\Cron
     */
    public function logClean()
    {
        if (!\Mage::getStoreConfigFlag(self::XML_PATH_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        $this->_errors = array();

        try {
            \Mage::getModel('Magento\Log\Model\Log')->clean();
        }
        catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
        }

        $this->_sendLogCleanEmail();

        return $this;
    }
}
