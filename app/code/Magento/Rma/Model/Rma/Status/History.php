<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Model\Rma\Status;

/**
 * RMA model
 */
class History extends \Magento\Core\Model\AbstractModel
{
    /**
     * Core store manager interface
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Rma factory
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * Rma configuration
     *
     * @var \Magento\Rma\Model\Config
     */
    protected $_rmaConfig;

    /**
     * Mail transport builder
     *
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Core date model
     *
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\Config $rmaConfig
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder ,
     * @param \Magento\Stdlib\DateTime\DateTime $date
     * @param \Magento\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Config $rmaConfig,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Stdlib\DateTime\DateTime $date,
        \Magento\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rmaFactory = $rmaFactory;
        $this->_rmaConfig = $rmaConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_date = $date;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
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
     * @return $this
     */
    public function sendCommentEmail()
    {
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

        return $this->_sendCommentEmail($this->_rmaConfig->getRootCommentEmail(), $sendTo, true);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @return $this
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
     * @return $this
     */
    public function _sendCommentEmail($rootConfig, $sendTo, $isGuestAvailable = true)
    {
        $this->_rmaConfig->init($rootConfig, $this->getStoreId());
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $order = $this->getRma()->getOrder();
        $comment = $this->getComment();

        $this->inlineTranslation->suspend();

        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();

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

        $bcc = array();
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(array(
                    'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ))
                ->setTemplateVars(array(
                    'rma'       => $this->getRma(),
                    'order'     => $this->getRma()->getOrder(),
                    'comment'   => $comment
                ))
                ->setFrom($this->_rmaConfig->getIdentity())
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();

            $transport->sendMessage();
        }
        $this->setEmailSent(true);

        $this->inlineTranslation->resume();

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
