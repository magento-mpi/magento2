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
 * @method \Magento\Indexer\Model\Indexer\State setIndexerId(string $value)
 * @method string getMode()
 * @method \Magento\Indexer\Model\Indexer\State setMode(string $value)
 * @method string getStatus()
 * @method \Magento\Indexer\Model\Indexer\State setStatus(string $value)
 * @method string getUpdated()
 * @method \Magento\Indexer\Model\Indexer\State setUpdated($value)
 */
class State extends \Magento\Core\Model\AbstractModel
{
    /**
     * Indexer modes
     */
    const MODE_ON_THE_FLY = 'onthefly';
    const MODE_CHANGELOG = 'changelog';

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
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Indexer\Model\Resource\Indexer\State $resource
     * @param \Magento\Indexer\Model\Resource\Indexer\State\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Indexer\Model\Resource\Indexer\State $resource,
        \Magento\Indexer\Model\Resource\Indexer\State\Collection $resourceCollection,
        array $data = array()
    ) {
        if (!isset($data['mode'])) {
            $data['mode'] = self::MODE_ON_THE_FLY;
        }
        if (!isset($data['status'])) {
            $data['status'] = self::STATUS_INVALID;
        }
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _beforeSave()
    {
        $this->setUpdated(time());
        return parent::_beforeSave();
    }
}
