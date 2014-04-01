<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model\Indexer;

/**
 * @method string getIndexerId()
 * @method \Magento\Indexer\Model\Indexer\State setIndexerId($value)
 * @method string getStatus()
 * @method string getUpdated()
 * @method \Magento\Indexer\Model\Indexer\State setUpdated($value)
 */
class State extends \Magento\Core\Model\AbstractModel
{
    /**
     * Indexer statuses
     */
    const STATUS_WORKING = 'working';

    const STATUS_VALID = 'valid';

    const STATUS_INVALID = 'invalid';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'indexer_state';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'indexer_state';

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Indexer\Model\Resource\Indexer\State $resource
     * @param \Magento\Indexer\Model\Resource\Indexer\State\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Indexer\Model\Resource\Indexer\State $resource,
        \Magento\Indexer\Model\Resource\Indexer\State\Collection $resourceCollection,
        array $data = array()
    ) {
        if (!isset($data['status'])) {
            $data['status'] = self::STATUS_INVALID;
        }
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Fill object with state data by view ID
     *
     * @param string $indexerId
     * @return $this
     */
    public function loadByIndexer($indexerId)
    {
        $this->load($indexerId, 'indexer_id');
        if (!$this->getId()) {
            $this->setIndexerId($indexerId);
        }
        return $this;
    }

    /**
     * Status setter
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return parent::setStatus($status);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $this->setUpdated(time());
        return parent::_beforeSave();
    }
}
