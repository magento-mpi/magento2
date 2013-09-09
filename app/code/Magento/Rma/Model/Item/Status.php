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
 * RMA Item Status Manager
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Item_Status extends Magento_Object
{
    /**
     * Artificial "maximal" item status when whole order is closed
     */
    const STATUS_ORDER_IS_CLOSED = 'order_is_closed';

    /**
     * Artificial "minimal" item status when all allowed fields are editable
     */
    const STATUS_ALL_ARE_EDITABLE = 'all_are_editable';

    /**
     * Flag for artificial statuses
     *
     * @var bool
     */
    protected $_isSpecialStatus = false;

    /**
     * Get options array for display in grid, consisting only from allowed statuses
     *
     * @return array
     */
    public function getAllowedStatuses()
    {
        $statusesAllowed = array(
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING,
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED,
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED
            ),
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED,
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED
            ),
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED,
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED,
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_REJECTED
            ),
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED
            ),
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_REJECTED => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_REJECTED
            ),
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED => array(
                Magento_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED
            ),
        );
        $boundingArray = isset($statusesAllowed[$this->getStatus()])
            ? $statusesAllowed[$this->getStatus()]
            : array();
        return
            array_intersect_key(
                Mage::getSingleton('Magento_Rma_Model_Item_Attribute_Source_Status')->getAllOptionsForGrid(),
                array_flip($boundingArray)
            );
    }

    /**
     * Get item status sequence - linear order on item statuses set
     *
     * @return array
     */
    protected function _getStatusSequence()
    {
        return array(
            self::STATUS_ALL_ARE_EDITABLE,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_REJECTED,
            Magento_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED,
            self::STATUS_ORDER_IS_CLOSED,
        );
    }

    /**
     * Get Border status for each attribute.
     *
     * For statuses, "less" than border status, attribute becomes uneditable
     * For statuses, "equal or greater" than border status, attribute becomes editable
     *
     * @param  $attribute
     * @return string
     */
    public function getBorderStatus($attribute)
    {
        switch ($attribute) {
            case 'qty_requested':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
                break;
            case 'qty_authorized':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED;
                break;
            case 'qty_returned':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED;
                break;
            case 'qty_approved':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED;
                break;
            case 'reason':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
                break;
            case 'condition':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
                break;
            case 'resolution':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED;
                break;
            case 'status':
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED;
                break;
            case 'action':
                return self::STATUS_ORDER_IS_CLOSED;
                break;
            default:
                return Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
                break;
        }
    }

    /**
     * Get whether attribute is editable
     *
     * @param string $attribute
     * @return bool
     */
    public function getAttributeIsEditable($attribute)
    {
        $typeSequence = $this->_getStatusSequence();
        $itemStateKey = array_search($this->getSequenceStatus(), $typeSequence);
        if ($itemStateKey === false) {
            return false;
        }

        if (array_search($this->getBorderStatus($attribute), $typeSequence) > $itemStateKey){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get whether editable attribute is disabled
     *
     * @param string $attribute
     * @return bool
     */
    public function getAttributeIsDisabled($attribute)
    {
        if($this->getSequenceStatus() == self::STATUS_ALL_ARE_EDITABLE) {
            return false;
        }

        switch ($attribute) {
            case 'qty_authorized':
                $enabledStatus = Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
                break;
            case 'qty_returned':
                $enabledStatus = Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED;
                break;
            case 'qty_approved':
                $enabledStatus = Magento_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED;
                break;
            default:
                return false;
                break;
        }

        if ($enabledStatus == $this->getSequenceStatus()){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sets "maximal" status for closed orders
     *
     * For closed orders no attributes should be editable.
     * So this method sets item status to artificial "maximum" value
     *
     * @return void
     */
    public function setOrderIsClosed()
    {
        $this->setSequenceStatus(self::STATUS_ORDER_IS_CLOSED);
        $this->_isSpecialStatus = true;
    }

    /**
     * Sets "minimal" status
     *
     * For split line functionality all fields must be editable
     *
     * @return void
     */
    public function setAllEditable()
    {
        $this->setSequenceStatus(self::STATUS_ALL_ARE_EDITABLE);
        $this->_isSpecialStatus = true;
    }

    /**
     * Sets status to object but not for self::STATUS_ORDER_IS_CLOSED status
     *
     * @param  $status
     * @return Magento_Rma_Model_Item_Status
     */
    public function setStatus($status)
    {
        if (!$this->getSequenceStatus() || !$this->_isSpecialStatus) {
            $this->setSequenceStatus($status);
        }
        return parent::setStatus($status);
    }

}
