<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Comment;

use Magento\Sales\Api\Data\CreditmemoCommentSearchResultInterface;
use Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection;

/**
 * Flat sales order creditmemo comments collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection implements CreditmemoCommentSearchResultInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_creditmemo_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_creditmemo_comment_collection';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            'Magento\Sales\Model\Order\Creditmemo\Comment',
            'Magento\Sales\Model\Resource\Order\Creditmemo\Comment'
        );
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        return $this->setParentFilter($creditmemoId);
    }
}
