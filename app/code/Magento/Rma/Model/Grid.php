<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model;

/**
 * RMA model
 */
class Grid extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Rma source status factory
     *
     * @var \Magento\Rma\Model\Rma\Source\StatusFactory
     */
    protected $_statusFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Rma\Model\Rma\Source\StatusFactory $statusFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Rma\Model\Rma\Source\StatusFactory $statusFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_statusFactory = $statusFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Grid');
        parent::_construct();
    }

    /**
     * Get available states keys for items
     *
     * @return string[]
     */
    protected function _getAvailableStates()
    {
        return array(
            self::STATE_PENDING,
            self::STATE_AUTHORIZED,
            self::STATE_RECEIVED,
            self::STATE_APPROVED,
            self::STATE_DENIED,
            self::STATE_REJECTED,
            self::STATE_CLOSED
        );
    }

    /**
     * Get RMA's status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            /** @var $sourceStatus \Magento\Rma\Model\Rma\Source\Status */
            $sourceStatus = $this->_statusFactory->create();
            $this->setStatusLabel($sourceStatus->getItemLabel($this->getStatus()));
        }
        return parent::getStatusLabel();
    }
}
