<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma\Status;

use Magento\Rma\Model\Rma;
use Magento\Rma\Model\Rma\Source\Status;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * RMA model
 * @method \Magento\Rma\Model\Rma\Status\History setRma(\Magento\Rma\Model\Rma $value)
 * @method \Magento\Rma\Model\Rma\Status\History setIsCustomerNotified(bool $value)
 * @method \Magento\Rma\Model\Rma\Status\History setComment(string $comment)
 * @method \Magento\Rma\Model\Rma\Status\History setStoreId(int $storeId)
 * @method \Magento\Rma\Model\Rma\Status\History setEmailSent(bool $value)
 * @method bool getEmailSent()
 * @method string getCreatedAt()
 */
class History extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Core store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
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
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Core date model
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Core date model 2.0
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTimeDateTime;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Rma\Helper\Data
     */
    protected $rmaHelper;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\Config $rmaConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTimeDateTime
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Config $rmaConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTimeDateTime,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Rma\Helper\Data $rmaHelper,
        TimezoneInterface $localeDate,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_rmaFactory = $rmaFactory;
        $this->_rmaConfig = $rmaConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->dateTime = $dateTime;
        $this->dateTimeDateTime = $dateTimeDateTime;
        $this->inlineTranslation = $inlineTranslation;
        $this->rmaHelper = $rmaHelper;
        $this->localeDate = $localeDate;
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
     * @return \Magento\Store\Model\Store
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
        $sendTo = array(array('email' => $order->getCustomerEmail(), 'name' => $customerName));

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
                'email' => $this->_rmaConfig->getCustomerEmailRecipient($this->getRma()->getStoreId()),
                'name' => null
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
    protected function _sendCommentEmail($rootConfig, $sendTo, $isGuestAvailable = true)
    {
        $rma = $this->getRma();

        $this->_rmaConfig->init($rootConfig, $rma->getStoreId());
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();

        if ($isGuestAvailable && $rma->getOrder()->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
        } else {
            $template = $this->_rmaConfig->getTemplate();
        }

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array('email' => $email, 'name' => null);
            }
        }

        $bcc = array();
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $rma->getStoreId()]
                )
                ->setTemplateVars(['rma' => $rma, 'order' => $rma->getOrder(), 'comment' => $this->getComment()])
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
        $status = $this->getRma()->getStatus();
        $comment = self::getSystemCommentByStatus($status);
        if ($status === $this->getRma()->getOrigData('status') && $comment) {
            return;
        }
        $this->saveComment($comment, true, true);
    }

    /**
     * Get system comment by state
     * Returns null if state is not known
     *
     * @param string $status
     * @return string|null
     */
    public static function getSystemCommentByStatus($status)
    {
        $comments = [
            Status::STATE_PENDING => __('We placed your Return request.'),
            Status::STATE_AUTHORIZED => __('We have authorized your Return request.'),
            Status::STATE_PARTIAL_AUTHORIZED => __('We partially authorized your Return request.'),
            Status::STATE_RECEIVED => __('We received your Return request.'),
            Status::STATE_RECEIVED_ON_ITEM => __('We partially received your Return request.'),
            Status::STATE_APPROVED_ON_ITEM => __('We partially approved your Return request.'),
            Status::STATE_REJECTED_ON_ITEM => __('We partially rejected your Return request.'),
            Status::STATE_CLOSED => __('We closed your Return request.'),
            Status::STATE_PROCESSED_CLOSED => __('We processed and closed your Return request.')
        ];
        return isset($comments[$status]) ? $comments[$status] : null;
    }

    /**
     * @param string $comment
     * @param bool $visibleOnFrontend
     * @param bool $isAdmin
     * @return void
     */
    public function saveComment($comment, $visibleOnFrontend, $isAdmin = false)
    {
        $rma = $this->getRma();
        $this->setRmaEntityId($rma->getId())
            ->setComment($comment)
            ->setIsVisibleOnFront($visibleOnFrontend)
            ->setStatus($rma->getStatus())
            ->setCreatedAt($this->dateTimeDateTime->gmtDate())
            ->setIsCustomerNotified($this->getEmailSent())
            ->setIsAdmin($isAdmin)
            ->save();
    }

    /**
     * Sending email with RMA data
     *
     * @return $this
     */
    public function sendNewRmaEmail()
    {
        return $this->_sendRmaEmailWithItems($this->getRma(), $this->_rmaConfig->getRootRmaEmail());
    }

    /**
     * Sending authorizing email with RMA data
     *
     * @return $this
     */
    public function sendAuthorizeEmail()
    {
        $rma = $this->getRma();
        if (!$rma->getIsSendAuthEmail()) {
            return $this;
        }
        return $this->_sendRmaEmailWithItems($rma, $this->_rmaConfig->getRootAuthEmail());
    }

    /**
     * Sending authorizing email with RMA data
     *
     * @param Rma $rma
     * @param string $rootConfig
     * @return $this
     */
    protected function _sendRmaEmailWithItems(Rma $rma, $rootConfig)
    {
        $storeId = $rma->getStoreId();
        $order = $rma->getOrder();

        $this->_rmaConfig->init($rootConfig, $storeId);
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();

        if ($order->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = $this->_rmaConfig->getTemplate();
            $customerName = $rma->getCustomerName();
        }

        $sendTo = array(array('email' => $order->getCustomerEmail(), 'name' => $customerName));
        if ($rma->getCustomerCustomEmail()) {
            $sendTo[] = array('email' => $rma->getCustomerCustomEmail(), 'name' => $customerName);
        }
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array('email' => $email, 'name' => null);
            }
        }

        $returnAddress = $this->rmaHelper->getReturnAddress('html', array(), $storeId);

        $bcc = array();
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars(
                    [
                        'rma' => $this,
                        'order' => $order,
                        'return_address' => $returnAddress,
                        'item_collection' => $rma->getItemsForDisplay()
                    ]
                )
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
     * Get object created at date affected current active store timezone
     *
     * @return \Magento\Framework\Stdlib\DateTime\Date
     */
    public function getCreatedAtDate()
    {
        return $this->localeDate->date($this->dateTime->toTimestamp($this->getCreatedAt()), null, null, true);
    }
}
