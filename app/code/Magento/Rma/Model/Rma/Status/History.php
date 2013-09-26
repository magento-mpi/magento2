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
 */
namespace Magento\Rma\Model\Rma\Status;

class History extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * @var \Magento\Rma\Model\Config
     */
    protected $_rmaConfig;

    /**
     * @var \Magento\Core\Model\Translate\Proxy
     */
    protected $_translate;

    /**
     * @var \Magento\Core\Model\Email\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\Config $rmaConfig
     * @param \Magento\Core\Model\Translate\Proxy $translate
     * @param \Magento\Core\Model\Email\TemplateFactory $templateFactory
     * @param \Magento\Core\Model\Date $date
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Config $rmaConfig,
        \Magento\Core\Model\Translate\Proxy $translate,
        \Magento\Core\Model\Email\TemplateFactory $templateFactory,
        \Magento\Core\Model\Date $date,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rmaFactory = $rmaFactory;
        $this->_rmaConfig = $rmaConfig;
        $this->_translate = $translate;
        $this->_templateFactory = $templateFactory;
        $this->_date = $date;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Rma\Status\History');
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
        return $this->_storeManager->getStore();
    }

    /**
     * Get RMA object
     *
     * @return \Magento\Rma\Model\Rma
     */
    public function getRma()
    {
        if (!$this->hasData('rma') && $this->getRmaEntityId()) {
            /** @var $rma \Magento\Rma\Model\Rma */
            $rma = $this->_rmaFactory->create();
            $rma->load($this->getRmaEntityId());
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
        $configRmaEmail = $this->_rmaConfig;
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
        $sendTo = array(
            array(
                'email' => $this->_rmaConfig->getCustomerEmailRecipient($this->getStoreId()),
                'name'  => null
            )
        );
        return $this->_sendCommentEmail($this->_rmaConfig->getRootCustomerCommentEmail(), $sendTo, false);
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
        $this->_rmaConfig->init($rootConfig, $this->getStoreId());
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $order = $this->getRma()->getOrder();
        $comment = $this->getComment();

        $this->_translate->setTranslateInline(false);
        /** @var $mailTemplate \Magento\Core\Model\Email\Template */
        $mailTemplate = $this->_templateFactory->create();
        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();
        if ($copyTo && $copyMethod == 'bcc') {
            foreach ($copyTo as $email) {
                $mailTemplate->addBcc($email);
            }
        }

        if ($isGuestAvailable && $order->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
        } else {
            $template = $this->_rmaConfig->getTemplate();
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
                    $this->_rmaConfig->getIdentity(),
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
        $this->_translate->setTranslateInline(true);

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
                ->setCreatedAt($this->_date->gmtDate())
                ->setIsAdmin(1)
                ->save();
        }
    }
}
