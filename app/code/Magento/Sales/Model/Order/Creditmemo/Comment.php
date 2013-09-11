<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo\Comment _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo\Comment getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Creditmemo\Comment setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Creditmemo;

class Comment extends \Magento\Sales\Model\AbstractModel
{
    /**
     * Creditmemo instance
     *
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $_creditmemo;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Creditmemo\Comment');
    }

    /**
     * Declare Creditmemo instance
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  \Magento\Sales\Model\Order\Creditmemo\Comment
     */
    public function setCreditmemo(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve Creditmemo instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Get store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->getCreditmemo()) {
            return $this->getCreditmemo()->getStore();
        }
        return \Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return \Magento\Sales\Model\Order\Creditmemo\Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
