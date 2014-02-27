<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal resource model for certificate based authentication
 */
namespace Magento\Paypal\Model\Resource;

class Cert extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Stdlib\DateTime $dateTime
    ) {
        $this->_coreDate = $coreDate;
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     */
    protected function _construct()
    {
        $this->_init('paypal_cert', 'cert_id');
    }

    /**
     * Set date of last update
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate($this->_coreDate->gmtDate()));
        return parent::_beforeSave($object);
    }

    /**
     * Load model by website id
     *
     * @param \Magento\Paypal\Model\Cert $object
     * @param bool $strictLoad
     * @return \Magento\Paypal\Model\Cert
     */
    public function loadByWebsite($object, $strictLoad = true)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(array('main_table' => $this->getMainTable()));

        if ($strictLoad) {
            $select->where('main_table.website_id =?', $object->getWebsiteId());
        } else {
            $select->where('main_table.website_id IN(0, ?)', $object->getWebsiteId())
                ->order('main_table.website_id DESC')
                ->limit(1);
        }

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }
        return $object;
    }
}
