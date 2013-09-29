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
namespace Magento\Rma\Model;

class Grid extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Rma\Model\Rma\Source\StatusFactory
     */
    protected $_statusFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Model\Rma\Source\StatusFactory $statusFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Model\Rma\Source\StatusFactory $statusFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_statusFactory = $statusFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Grid');
        parent::_construct();
    }

    /**
     * Get available states keys for items
     *
     * @return array
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
