<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Resource\Payment\Transaction;

/**
 * Payment transactions collection
 *
 * @deprecated since 1.6.2.0
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Created Before filter
     *
     * @var string
     */
    protected $_createdBefore          = "";
    /**
     * Initialize collection items factory class
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Payment\Transaction', 'Magento\Paypal\Model\Resource\Payment\Transaction');
        parent::_construct();
    }

    /**
     * CreatedAt filter setter
     *
     * @param string $date
     * @return $this
     */
    public function addCreatedBeforeFilter($date)
    {
        $this->_createdBefore = $date;
        return $this;
    }

    /**
     * Prepare filters
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        if ($this->isLoaded()) {
            return $this;
        }

        // filters
        if ($this->_createdBefore) {
            $this->getSelect()->where('main_table.created_at < ?', $this->_createdBefore);
        }
        return $this;
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
