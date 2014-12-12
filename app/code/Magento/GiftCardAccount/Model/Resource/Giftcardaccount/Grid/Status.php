<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * GiftCardAccount Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Grid;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected $_model;

    /**
     * @param \Magento\GiftCardAccount\Model\Giftcardaccount $model
     */
    public function __construct(\Magento\GiftCardAccount\Model\Giftcardaccount $model)
    {
        $this->_model = $model;
    }

    /**
     * Return states options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->getStatesAsOptionList();
    }
}
