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
 * Flat sales order creditmemo comments collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Comment;

class Collection
    extends \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_comment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Sales\Model\Order\Creditmemo\Comment', 'Magento\Sales\Model\Resource\Order\Creditmemo\Comment');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return \Magento\Sales\Model\Resource\Order\Creditmemo\Comment\Collection
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        return $this->setParentFilter($creditmemoId);
    }
}
