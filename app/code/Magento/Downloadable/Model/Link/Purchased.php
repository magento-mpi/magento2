<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Link;

/**
 * Downloadable links purchased model
 *
 * @method \Magento\Downloadable\Model\Resource\Link\Purchased _getResource()
 * @method \Magento\Downloadable\Model\Resource\Link\Purchased getResource()
 * @method int getOrderId()
 * @method \Magento\Downloadable\Model\Link\Purchased setOrderId(int $value)
 * @method string getOrderIncrementId()
 * @method \Magento\Downloadable\Model\Link\Purchased setOrderIncrementId(string $value)
 * @method int getOrderItemId()
 * @method \Magento\Downloadable\Model\Link\Purchased setOrderItemId(int $value)
 * @method string getCreatedAt()
 * @method \Magento\Downloadable\Model\Link\Purchased setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Downloadable\Model\Link\Purchased setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method \Magento\Downloadable\Model\Link\Purchased setCustomerId(int $value)
 * @method string getProductName()
 * @method \Magento\Downloadable\Model\Link\Purchased setProductName(string $value)
 * @method string getProductSku()
 * @method \Magento\Downloadable\Model\Link\Purchased setProductSku(string $value)
 * @method string getLinkSectionTitle()
 * @method \Magento\Downloadable\Model\Link\Purchased setLinkSectionTitle(string $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Purchased extends \Magento\Core\Model\AbstractModel
{
    /**
     * Enter description here...
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Downloadable\Model\Resource\Link\Purchased');
        parent::_construct();
    }

    /**
     * Check order id
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function _beforeSave()
    {
        if (null == $this->getOrderId()) {
            throw new \Exception(
                __('Order id cannot be null'));
        }
        return parent::_beforeSave();
    }

}
