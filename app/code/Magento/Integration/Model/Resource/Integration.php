<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Integration resource model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Integration\Model\Resource;

class Integration extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('integration', 'integration_id');
    }

    /**
     * Set updated_at automatically before saving
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Integration\Model\Resource\Integration
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }
}
