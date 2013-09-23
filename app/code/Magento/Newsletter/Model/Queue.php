<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter queue model.
 *
 * @method Magento_Newsletter_Model_Resource_Queue _getResource()
 * @method Magento_Newsletter_Model_Resource_Queue getResource()
 * @method int getTemplateId()
 * @method Magento_Newsletter_Model_Queue setTemplateId(int $value)
 * @method int getNewsletterType()
 * @method Magento_Newsletter_Model_Queue setNewsletterType(int $value)
 * @method string getNewsletterText()
 * @method Magento_Newsletter_Model_Queue setNewsletterText(string $value)
 * @method string getNewsletterStyles()
 * @method Magento_Newsletter_Model_Queue setNewsletterStyles(string $value)
 * @method string getNewsletterSubject()
 * @method Magento_Newsletter_Model_Queue setNewsletterSubject(string $value)
 * @method string getNewsletterSenderName()
 * @method Magento_Newsletter_Model_Queue setNewsletterSenderName(string $value)
 * @method string getNewsletterSenderEmail()
 * @method Magento_Newsletter_Model_Queue setNewsletterSenderEmail(string $value)
 * @method int getQueueStatus()
 * @method Magento_Newsletter_Model_Queue setQueueStatus(int $value)
 * @method string getQueueStartAt()
 * @method Magento_Newsletter_Model_Queue setQueueStartAt(string $value)
 * @method string getQueueFinishAt()
 * @method Magento_Newsletter_Model_Queue setQueueFinishAt(string $value)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Newsletter_Model_Queue extends Magento_Core_Model_Template
{
    /**
     * Newsletter Template object
     *
     * @var Magento_Newsletter_Model_Template
     */
    protected $_template;

    /**
     * @var Magento_Core_Model_Email_Template
     */
    protected $_emailTemplate = null;

    /**
     * Subscribers collection
     *
     * @var Magento_Newsletter_Model_Resource_Subscriber_Collection
     */
    protected $_subscribersCollection;

    /**
     * Save stores flag.
     *
     * @var boolean
     */
    protected $_saveStoresFlag = false;

    /**
     * Stores assigned to queue.
     *
     * @var array
     */
    protected $_stores = array();

    const STATUS_NEVER = 0;
    const STATUS_SENDING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_SENT = 3;
    const STATUS_PAUSE = 4;

    /**
     * Filter for newsletter text
     *
     * @var Magento_Newsletter_Model_Template_Filter
     */
    protected $_templateFilter;

    /**
     * Date
     *
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Email template factory
     *
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_emailTemplateFactory;

    /**
     * Problem factory
     *
     * @var Magento_Newsletter_Model_ProblemFactory
     */
    protected $_problemFactory;

    /**
     * Template factory
     *
     * @var Magento_Newsletter_Model_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Newsletter_Model_Template_Filter $templateFilter
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Date $date
     * @param Magento_Newsletter_Model_TemplateFactory $templateFactory
     * @param Magento_Newsletter_Model_ProblemFactory $problemFactory
     * @param Magento_Core_Model_Email_TemplateFactory $emailTemplateFactory
     * @param Magento_Newsletter_Model_Resource_Subscriber_CollectionFactory $subscriberCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Newsletter_Model_Template_Filter $templateFilter,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Date $date,
        Magento_Newsletter_Model_TemplateFactory $templateFactory,
        Magento_Newsletter_Model_ProblemFactory $problemFactory,
        Magento_Core_Model_Email_TemplateFactory $emailTemplateFactory,
        Magento_Newsletter_Model_Resource_Subscriber_CollectionFactory $subscriberCollectionFactory,
        array $data = array()
    ) {
        parent::__construct($design, $context, $registry, $data);
        $this->_templateFilter = $templateFilter;
        $this->_date = $date;
        $this->_locale = $locale;
        $this->_templateFactory = $templateFactory;
        $this->_problemFactory = $problemFactory;
        $this->_emailTemplateFactory = $emailTemplateFactory;
        $this->_subscribersCollection = $subscriberCollectionFactory->create();
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $emailTemplate = $this->_getData('email_template');
        if ($emailTemplate) {
            $this->unsetData('email_template');
            if (!($emailTemplate instanceof Magento_Core_Model_Email_Template)) {
                throw new Exception('Instance of Magento_Core_Model_Email_Template is expected.');
            }
            $this->_emailTemplate = $emailTemplate;
        }
        $this->_init('Magento_Newsletter_Model_Resource_Queue');
    }

    /**
     * Return: is this queue newly created or not.
     *
     * @return boolean
     */
    public function isNew()
    {
        return (is_null($this->getQueueStatus()));
    }

    /**
     * Set $_data['queue_start'] based on string from backend, which based on locale.
     *
     * @param string|null $startAt start date of the mailing queue
     * @return Magento_Newsletter_Model_Queue
     */
    public function setQueueStartAtByString($startAt)
    {
        if (is_null($startAt) || $startAt == '') {
            $this->setQueueStartAt(null);
        } else {
            $format = $this->_locale->getDateTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
            $time = $this->_locale->date($startAt, $format)->getTimestamp();
            $this->setQueueStartAt($this->_date->gmtDate(null, $time));
        }
        return $this;
    }

    /**
     * Send messages to subscribers for this queue
     *
     * @param   int     $count
     * @param   array   $additionalVariables
     * @return Magento_Newsletter_Model_Queue
     */
    public function sendPerSubscriber($count = 20, array $additionalVariables = array())
    {
        if ($this->getQueueStatus() != self::STATUS_SENDING
           && ($this->getQueueStatus() != self::STATUS_NEVER && $this->getQueueStartAt())
        ) {
            return $this;
        }

        if (!$this->_subscribersCollection->getQueueJoinedFlag()) {
            $this->_subscribersCollection->useQueue($this);
        }

        if ($this->_subscribersCollection->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }

        $collection = $this->_subscribersCollection
            ->useOnlyUnsent()
            ->showCustomerInfo()
            ->setPageSize($count)
            ->setCurPage(1)
            ->load();

        /** @var Magento_Core_Model_Email_Template $sender */
        $sender = $this->_emailTemplate ?: $this->_emailTemplateFactory->create();
        $sender->setSenderName($this->getNewsletterSenderName())
            ->setSenderEmail($this->getNewsletterSenderEmail())
            ->setTemplateType(self::TYPE_HTML)
            ->setTemplateSubject($this->getNewsletterSubject())
            ->setTemplateText($this->getNewsletterText())
            ->setTemplateStyles($this->getNewsletterStyles())
            ->setTemplateFilter($this->_templateFilter);

        /** @var Magento_Newsletter_Model_Subscriber $item */
        foreach ($collection->getItems() as $item) {
            $email = $item->getSubscriberEmail();
            $name = $item->getSubscriberFullName();

            $sender->emulateDesign($item->getStoreId());
            $successSend = $sender->send($email, $name, array('subscriber' => $item));
            $sender->revertDesign();

            if ($successSend) {
                $item->received($this);
            } else {
                /** @var Magento_Newsletter_Model_Problem $problem */
                $problem = $this->_problemFactory->create();
                $problem->addSubscriberData($item);
                $problem->addQueueData($this);
                $e = $sender->getSendingException();
                if ($e) {
                    $problem->addErrorData($e);
                }
                $problem->save();
                $item->received($this);
            }
        }

        if (count($collection->getItems()) < $count-1 || count($collection->getItems()) == 0) {
            $this->_finishQueue();
        }
        return $this;
    }

    /**
     * Finish queue: set status SENT and update finish date
     *
     * @return Magento_Newsletter_Model_Queue
     */
    protected function _finishQueue()
    {
        $this->setQueueFinishAt($this->_date->gmtDate());
        $this->setQueueStatus(self::STATUS_SENT);
        $this->save();

        return $this;
    }

    /**
     * Getter data for saving
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data['template_id'] = $this->getTemplateId();
        $data['queue_status'] = $this->getQueueStatus();
        $data['queue_start_at'] = $this->getQueueStartAt();
        $data['queue_finish_at'] = $this->getQueueFinishAt();
        return $data;
    }

    /**
     * Add subscribers to queue.
     *
     * @param array $subscriberIds
     * @return Magento_Newsletter_Model_Queue
     */
    public function addSubscribersToQueue(array $subscriberIds)
    {
        $this->_getResource()->addSubscribersToQueue($this, $subscriberIds);
        return $this;
    }

    /**
     * Setter for save stores flag.
     *
     * @param boolean|integer|string $value
     * @return Magento_Newsletter_Model_Queue
     */
    public function setSaveStoresFlag($value)
    {
        $this->_saveStoresFlag = (boolean)$value;
        return $this;
    }

    /**
     * Getter for save stores flag.
     *
     * @param void
     * @return boolean
     */
    public function getSaveStoresFlag()
    {
        return $this->_saveStoresFlag;
    }

    /**
     * Setter for stores of queue.
     *
     * @param array
     * @return Magento_Newsletter_Model_Queue
     */
    public function setStores(array $storesIds)
    {
        $this->setSaveStoresFlag(true);
        $this->_stores = $storesIds;
        return $this;
    }

    /**
     * Getter for stores of queue.
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = $this->_getResource()->getStores($this);
        }

        return $this->_stores;
    }

    /**
     * Retrieve Newsletter Template object
     *
     * @return Magento_Newsletter_Model_Template
     */
    public function getTemplate()
    {
        if (is_null($this->_template)) {
            $this->_template = $this->_templateFactory->create()
                ->load($this->getTemplateId());
        }
        return $this->_template;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getNewsletterType();
    }
}
