<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model;

/**
 * Logging event model
 */
class Event extends \Magento\Framework\Model\AbstractModel
{
    const RESULT_SUCCESS = 'success';

    const RESULT_FAILURE = 'failure';

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_userFactory = $userFactory;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\Logging\Model\Resource\Event');
    }

    /**
     * Set some data automatically before saving model
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setStatus($this->getIsSuccess() ? self::RESULT_SUCCESS : self::RESULT_FAILURE);
            if (!$this->getUser() && ($id = $this->getUserId())) {
                $this->setUser($this->_userFactory->create()->load($id)->getUserName());
            }
            if (!$this->hasTime()) {
                $this->setTime(time());
            }
        }
        /**
         * Prepare short details data
         */
        $info = array();
        $info['general'] = $this->getInfo();
        if ($this->getAdditionalInfo()) {
            $info['additional'] = $this->getAdditionalInfo();
        }
        $this->setInfo(serialize($info));
        return parent::_beforeSave();
    }

    /**
     * Define if current event has event changes
     *
     * @return bool
     */
    public function hasChanges()
    {
        if ($this->getId()) {
            return (bool)$this->getResource()->getEventChangeIds($this->getId());
        }
        return false;
    }
}
