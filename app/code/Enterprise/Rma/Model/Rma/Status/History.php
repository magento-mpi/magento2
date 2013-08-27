<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Rma_Status_History extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Rma_Model_Resource_Rma_Status_History');
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Get RMA object
     *
     * @return Enterprise_Rma_Model_Rma;
     */
    public function getRma()
    {
        if (!$this->hasData('rma') && $this->getRmaEntityId()) {
            $rma = Mage::getModel('Enterprise_Rma_Model_Rma')->load($this->getRmaEntityId());
            $this->setData('rma', $rma);
        }
        return $this->getData('rma');
    }

    /**
     * Sending email with comment data
     *
     * @return Enterprise_Rma_Model_Rma_Status_History
     */
    public function sendCommentEmail()
    {
        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
        $configRmaEmail = Mage::getSingleton('Enterprise_Rma_Model_Config');
        $order = $this->getRma()->getOrder();
        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }
        $sendTo = array(
            array(
                'email' => $order->getCustomerEmail(),
                'name'  => $customerName
            )
        );

        return $this->_sendCommentEmail($configRmaEmail->getRootCommentEmail(), $sendTo, true);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @return Enterprise_Rma_Model_Rma_Status_History
     */
    public function sendCustomerCommentEmail()
    {
        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
        $configRmaEmail = Mage::getSingleton('Enterprise_Rma_Model_Config');
        $sendTo = array(
            array(
                'email' => $configRmaEmail->getCustomerEmailRecipient($this->getStoreId()),
                'name'  => null
            )
        );

        return $this->_sendCommentEmail($configRmaEmail->getRootCustomerCommentEmail(), $sendTo, false);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @param string $rootConfig Current config root
     * @param array $sendTo mail recipient array
     * @param bool $isGuestAvailable
     * @return Enterprise_Rma_Model_Rma_Status_History
     */
    public function _sendCommentEmail($rootConfig, $sendTo, $isGuestAvailable = true)
    {
        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
        $configRmaEmail = Mage::getSingleton('Enterprise_Rma_Model_Config');
        $configRmaEmail->init($rootConfig, $this->getStoreId());

        if (!$configRmaEmail->isEnabled()) {
            return $this;
        }

        $order = $this->getRma()->getOrder();
        $comment = $this->getComment();

        $translate = Mage::getSingleton('Magento_Core_Model_Translate');
        /* @var $translate Magento_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('Magento_Core_Model_Email_Template');
        /* @var $mailTemplate Magento_Core_Model_Email_Template */
        $copyTo = $configRmaEmail->getCopyTo();
        $copyMethod = $configRmaEmail->getCopyMethod();
        if ($copyTo && $copyMethod == 'bcc') {
            foreach ($copyTo as $email) {
                $mailTemplate->addBcc($email);
            }
        }

        if ($isGuestAvailable && $order->getCustomerIsGuest()) {
            $template = $configRmaEmail->getGuestTemplate();
        } else {
            $template = $configRmaEmail->getTemplate();
        }

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'email' => $email,
                    'name'  => null
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $this->getStoreId()
            ))
                ->sendTransactional(
                    $template,
                    $configRmaEmail->getIdentity(),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'rma'       => $this->getRma(),
                        'order'     => $this->getRma()->getOrder(),
                        'comment'   => $comment
                    )
                );
        }
        $this->setEmailSent(true);
        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Save system comment
     *
     * @return null
     */
    public function saveSystemComment()
    {
        $systemComments = array(
            Enterprise_Rma_Model_Rma_Source_Status::STATE_PENDING =>
                __('We placed your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED =>
                __('We have authorized your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_PARTIAL_AUTHORIZED =>
                __('We partially authorized your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_RECEIVED =>
                __('We received your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_RECEIVED_ON_ITEM =>
                __('We partially received your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_APPROVED_ON_ITEM =>
                __('We partially approved your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_REJECTED_ON_ITEM =>
                __('We partially rejected your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED =>
                __('We closed your Return request.'),
            Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED =>
                __('We processed and closed your Return request.'),
        );

        $rma = $this->getRma();
        if (!($rma instanceof Enterprise_Rma_Model_Rma)) {
            return;
        }

        if (($rma->getStatus() !== $rma->getOrigData('status') && isset($systemComments[$rma->getStatus()]))) {
            $this->setRmaEntityId($rma->getEntityId())
                ->setComment($systemComments[$rma->getStatus()])
                ->setIsVisibleOnFront(true)
                ->setStatus($rma->getStatus())
                ->setCreatedAt(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate())
                ->setIsAdmin(1)
                ->save();
        }
    }
}
