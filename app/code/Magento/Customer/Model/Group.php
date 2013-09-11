<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group model
 *
 * @method \Magento\Customer\Model\Resource\Group _getResource()
 * @method \Magento\Customer\Model\Resource\Group getResource()
 * @method string getCustomerGroupCode()
 * @method \Magento\Customer\Model\Group setCustomerGroupCode(string $value)
 * @method \Magento\Customer\Model\Group setTaxClassId(int $value)
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model;

class Group extends \Magento\Core\Model\AbstractModel
{
    /**
     * Xml config path for create account default group
     */
    const XML_PATH_DEFAULT_ID       = 'customer/create_account/default_group';

    const NOT_LOGGED_IN_ID          = 0;
    const CUST_GROUP_ALL            = 32000;

    const ENTITY                    = 'customer_group';

    const GROUP_CODE_MAX_LENGTH     = 32;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_group';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    protected static $_taxClassIds = array();

    protected function _construct()
    {
        $this->_init('\Magento\Customer\Model\Resource\Group');
    }

    /**
     * Alias for setCustomerGroupCode
     *
     * @param string $value
     */
    public function setCode($value)
    {
        return $this->setCustomerGroupCode($value);
    }

    /**
     * Alias for getCustomerGroupCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getCustomerGroupCode();
    }

    public function getTaxClassId($groupId = null)
    {
        if (!is_null($groupId)) {
            if (empty(self::$_taxClassIds[$groupId])) {
                $this->load($groupId);
                self::$_taxClassIds[$groupId] = $this->getData('tax_class_id');
            }
            $this->setData('tax_class_id', self::$_taxClassIds[$groupId]);
        }
        return $this->getData('tax_class_id');
    }


    public function usesAsDefault()
    {
        $data = \Mage::getConfig()->getStoresConfigByPath(self::XML_PATH_DEFAULT_ID);
        if (in_array($this->getId(), $data)) {
            return true;
        }
        return false;
    }

    /**
     * Run reindex process after data save
     *
     * @return \Magento\Customer\Model\Group
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        \Mage::getSingleton('Magento\Index\Model\Indexer')->processEntityAction(
            $this, self::ENTITY, \Magento\Index\Model\Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Prepare data before save
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        $this->_prepareData();
        return parent::_beforeSave();
    }

    /**
     * Prepare customer group data
     *
     * @return \Magento\Customer\Model\Group
     */
    protected function _prepareData()
    {
        $this->setCode(
            substr($this->getCode(), 0, self::GROUP_CODE_MAX_LENGTH)
        );
        return $this;
    }

}
