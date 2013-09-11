<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Rma\Status;

class History extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Rma\Model\Resource\Rma\Status\History');
    }

    /**
     * Get store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return \Mage::app()->getStore();
    }

    /**
     * Get RMA object
     *
     * @return \Magento\Rma\Model\Rma;
     */
    public function getRma()
    {
        if (!$this->hasData('rma') && $this->getRmaEntityId()) {
            $rma = \Mage::getModel('\Magento\Rma\Model\Rma')->load($this->getRmaEntityId());
            $this->setData('rma', $rma);
        }
        return $this->getData('rma');
    }

    /**
     * Sending email with comment data
     *
     * @return \Magento\Rma\Model\Rma\Status\History
     */
    public function sendCommentEmail()
    {
        /** @var $configRmaEmail \Magento\Rma\Model\Config */
        $configRmaEmail = \Mage::getSingleton('Magento\Rma\Model\Config');
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
     * @return \Magento\Rma\Model\Rma\Status\History
     */
    public function sendCustomerCommentEmail()
    {
        /** @var $configRmaEmail \Magento\Rma\Model\Config */
        $configRmaEmail = \Mage::getSingleton('Magento\Rma\Model\Config');
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
     * @return \Magento\Rma\Model\Rma\Status\History
     */
    public function _sendCommentEmail($rootConfig, $sendTo, $isGuestAvailable = true)
    {
        /** @var $configRmaEmail \Magento\Rma\Model\Config */
        $configRmaEmail = \Mage::getSingleton('Magento\Rma\Model\Config');
        $configRmaEmail->init($rootConfig, $this->getStoreId());

        if (!$configRmaEmail->isEnabled()) {
            return $this;
        }

        $order = $this->getRma()->getOrder();
        $comment = $this->getComment();

        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        /* @var $translate \Magento\Core\Model\Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = \Mage::getModel('\Magento\Core\Model\Email\Template');
        /* @var $mailTemplate \Magento\Core\Model\Email\Template */
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
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
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
            \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING =>
                __('We placed your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_AUTHORIZED =>
                __('We have authorized your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_PARTIAL_AUTHORIZED =>
                __('We partially authorized your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_RECEIVED =>
                __('We received your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_RECEIVED_ON_ITEM =>
                __('We partially received your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_APPROVED_ON_ITEM =>
                __('We partially approved your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_REJECTED_ON_ITEM =>
                __('We partially rejected your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_CLOSED =>
                __('We closed your Return request.'),
            \Magento\Rma\Model\Rma\Source\Status::STATE_PROCESSED_CLOSED =>
                __('We processed and closed your Return request.'),
        );

        $rma = $this->getRma();
        if (!($rma instanceof \Magento\Rma\Model\Rma)) {
            return;
        }

        if (($rma->getStatus() !== $rma->getOrigData('status') && isset($systemComments[$rma->getStatus()]))) {
            $this->setRmaEntityId($rma->getEntityId())
                ->setComment($systemComments[$rma->getStatus()])
                ->setIsVisibleOnFront(true)
                ->setStatus($rma->getStatus())
                ->setCreatedAt(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate())
                ->setIsAdmin(1)
                ->save();
        }
    }
}
