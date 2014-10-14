<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model;

/**
 * RMA Item model
 */
class Item extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'rma_item';

    /**
     * Rma instance
     *
     * @var \Magento\Rma\Model\Rma
     */
    protected $_rma = null;

    /**
     * Store firstly set all item attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Rma item $_FILES collection
     *
     * @var array
     */
    protected $_filesArray = array();

    /**
     * Rma item errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Image url
     */
    const ITEM_IMAGE_URL = 'rma_item';

    /**
     * Rma factory
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * Rma item attribute status factory
     *
     * @var \Magento\Rma\Model\Item\Attribute\Source\StatusFactory
     */
    protected $_statusFactory;

    /**
     * Sales order item factory
     *
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Rma item form factory
     *
     * @var \Magento\Rma\Model\Item\FormFactory
     */
    protected $_formFactory;

    /**
     * Application request factory
     *
     * @var \Magento\Framework\App\RequestFactory
     */
    protected $_requestFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\Item\Attribute\Source\StatusFactory $statusFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $itemFactory
     * @param \Magento\Rma\Model\Item\FormFactory $formFactory
     * @param \Magento\Framework\App\RequestFactory $requestFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Item\Attribute\Source\StatusFactory $statusFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Rma\Model\Item\FormFactory $formFactory,
        \Magento\Framework\App\RequestFactory $requestFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_rmaFactory = $rmaFactory;
        $this->_statusFactory = $statusFactory;
        $this->_itemFactory = $itemFactory;
        $this->_formFactory = $formFactory;
        $this->_requestFactory = $requestFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Item');
    }

    /**
     * Declare rma instance
     *
     * @param   Rma $rma
     * @return  $this
     */
    public function setRma(Rma $rma)
    {
        $this->_rma = $rma;
        $this->setRmaEntityId($rma->getId());
        return $this;
    }

    /**
     * Retrieve rma instance
     *
     * @return Rma
     */
    public function getRma()
    {
        $rmaId = $this->getRmaEntityId();
        if (is_null($this->_rma) && $rmaId) {
            /** @var $rma \Magento\Rma\Model\Rma */
            $rma = $this->_rmaFactory->create();
            $rma->load($rmaId);
            $this->setRma($rma);
        }
        return $this->_rma;
    }

    /**
     * Get RMA item's status label
     *
     * @return mixed
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            $this->setStatusLabel($this->_statusFactory->create()->getItemLabel($this->getStatus()));
        }
        return parent::getStatusLabel();
    }

    /**
     * Prepare data before save
     *
     * @return $this|void
     */
    public function beforeSave()
    {
        if (!$this->getRmaEntityId() && $this->getRma()) {
            $this->setRmaEntityId($this->getRma()->getId());
        }
        if ($this->getQtyAuthorized() === '') {
            $this->unsQtyAuthorized();
        }
        if ($this->getQtyReturned() === '') {
            $this->unsQtyReturned();
        }
        if ($this->getQtyApproved() === '') {
            $this->unsQtyApproved();
        }
        parent::beforeSave();
    }

    /**
     * Prepare data before save
     *
     * @return $this|void
     */
    public function afterSave()
    {
        $qtyReturnedChange = 0;
        if ($this->getOrigData('status') == \Magento\Rma\Model\Rma\Source\Status::STATE_APPROVED) {
            if ($this->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_APPROVED) {
                $qtyReturnedChange = $this->getQtyApproved() - $this->getOrigData('qty_approved');
            } else {
                $qtyReturnedChange = -$this->getOrigData('qty_approved');
            }
        } else {
            if ($this->getStatus() == \Magento\Rma\Model\Rma\Source\Status::STATE_APPROVED) {
                $qtyReturnedChange = $this->getQtyApproved();
            }
        }

        if ($qtyReturnedChange) {
            $item = $this->_itemFactory->create()->load($this->getOrderItemId());
            if ($item->getId()) {
                $item->setQtyReturned($item->getQtyReturned() + $qtyReturnedChange)->save();
            }
        }
        parent::afterSave();
    }

    /**
     * Retrieve all item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()->loadAllAttributes($this)->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Retrieve all item errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Get rma item attribute model object
     *
     * @param   string $attributeCode
     * @return  \Magento\Rma\Model\Item\Attribute | null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Prepares and adds $_POST data to item's attribute
     *
     * @param  array $itemPost
     * @param  int $key
     * @return string[]|null
     */
    public function prepareAttributes($itemPost, $key)
    {
        $httpRequest = $this->_requestFactory->create();
        $httpRequest->setPost($itemPost);

        /** @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = $this->_formFactory->create();
        $itemForm->setFormCode('default')->setEntity($this);
        $itemData = $itemForm->extractData($httpRequest);

        $files = array();
        foreach ($itemData as $code => &$value) {
            if (is_array($value) && empty($value)) {
                if (array_key_exists($code . '_' . $key, $_FILES)) {
                    $value = $_FILES[$code . '_' . $key];
                    $files[] = $code;
                }
            }
        }

        $itemErrors = $itemForm->validateData($itemData);
        if ($itemErrors !== true) {
            $this->_errors = array_merge($itemErrors, $this->_errors);
        } else {
            $itemForm->compactData($itemData);
        }

        if (!empty($files)) {
            foreach ($files as $code) {
                unset($_FILES[$code . '_' . $key]);
            }
            return $files;
        }
    }

    /**
     * Gets item options
     *
     * @return array|false
     */
    public function getOptions()
    {
        $result = array();
        $options = unserialize($this->getProductOptions());
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            return $result;
        }
        return false;
    }

    /**
     * Returns remaining qty of shipped items
     *
     * @param int $orderId
     * @param int $orderItemId
     * @return float|int
     */
    public function getReturnableQty($orderId = null, $orderItemId = null)
    {
        if (!$orderId) {
            $orderId = $this->getRma()->getOrderId();
        }
        if (!$orderItemId) {
            $orderItemId = $this->getOrderItemId();
        }
        $returnableItems = $this->getResource()->getReturnableItems($orderId);
        return isset($returnableItems[$orderItemId]) ? $returnableItems[$orderItemId] : 0;
    }
}
