<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Resource;

/**
 * Resource Model for Checkout Agreement
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Agreement extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Filter\FilterManager $filterManager
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Filter\FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
        parent::__construct($resource);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('checkout_agreement', 'agreement_id');
    }

    /**
     * Method to run before save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        // format height
        $height = $object->getContentHeight();
        $height = $this->filterManager->stripTags($height);
        if (!$height) {
            $height = '';
        }
        if ($height && preg_match('/[0-9]$/', $height)) {
            $height .= 'px';
        }
        $object->setContentHeight($height);
        return parent::_beforeSave($object);
    }

    /**
     * Method to run after save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Core\Model\AbstractModel $object)
    {
        $condition = array('agreement_id = ?' => $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('checkout_agreement_store'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['agreement_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('checkout_agreement_store'), $storeArray);
        }

        return parent::_afterSave($object);
    }

    /**
     * Method to run after load
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Core\Model\AbstractModel $object)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getTable('checkout_agreement_store'),
            array('store_id')
        )->where(
            'agreement_id = :agreement_id'
        );

        if ($stores = $this->_getReadAdapter()->fetchCol($select, array(':agreement_id' => $object->getId()))) {
            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get load select
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(
                array('cps' => $this->getTable('checkout_agreement_store')),
                $this->getMainTable() . '.agreement_id = cps.agreement_id'
            )->where(
                'is_active=1'
            )->where(
                'cps.store_id IN (0, ?)',
                $object->getStoreId()
            )->order(
                'store_id DESC'
            )->limit(
                1
            );
        }
        return $select;
    }
}
