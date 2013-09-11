<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Email Template Mailer Model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Email\Template;

class Mailer extends \Magento\Object
{
    /**
     * List of email infos
     * @see \Magento\Core\Model\Email\Info
     *
     * @var array
     */
    protected $_emailInfos = array();

    /**
     * Add new email info to corresponding list
     *
     * @param \Magento\Core\Model\Email\Info $emailInfo
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function addEmailInfo(\Magento\Core\Model\Email\Info $emailInfo)
    {
        array_push($this->_emailInfos, $emailInfo);
        return $this;
    }

    /**
     * Send all emails from email list
     * @see self::$_emailInfos
     *
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function send()
    {
        /** @var $emailTemplate \Magento\Core\Model\Email\Template */
        $emailTemplate = \Mage::getModel('Magento\Core\Model\Email\Template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to \Magento\Core\Model\Email\Template
            $designConfig = array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $this->getStoreId()
            );
            $emailTemplate->setDesignConfig($designConfig);

            $emailTemplate->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $emailInfo->getToEmails(),
                $emailInfo->getToNames(),
                $this->getTemplateParams(),
                $this->getStoreId()
            );
        }
        return $this;
    }

    /**
     * Set email sender
     *
     * @param string|array $sender
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function setSender($sender)
    {
        return $this->setData('sender', $sender);
    }

    /**
     * Get email sender
     *
     * @return string|array|null
     */
    public function getSender()
    {
        return $this->_getData('sender');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Set template id
     *
     * @param int $templateId
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function setTemplateId($templateId)
    {
        return $this->setData('template_id', $templateId);
    }

    /**
     * Get template id
     *
     * @return int|null
     */
    public function getTemplateId()
    {
        return $this->_getData('template_id');
    }

    /**
     * Set tempate parameters
     *
     * @param array $templateParams
     * @return \Magento\Core\Model\Email\Template\Mailer
     */
    public function setTemplateParams(array $templateParams)
    {
        return $this->setData('template_params', $templateParams);
    }

    /**
     * Get template parameters
     *
     * @return array|null
     */
    public function getTemplateParams()
    {
        return $this->_getData('template_params');
    }
}
