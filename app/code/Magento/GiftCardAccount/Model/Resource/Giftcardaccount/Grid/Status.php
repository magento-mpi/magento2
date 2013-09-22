<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Resource Collection
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Grid;

class Status
    implements \Magento\Core\Model\Option\ArrayInterface
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
