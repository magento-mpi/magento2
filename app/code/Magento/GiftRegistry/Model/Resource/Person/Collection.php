<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity registrants collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource\Person;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\GiftRegistry\Model\Person', '\Magento\GiftRegistry\Model\Resource\Person');
    }

    /**
     * Apply entity filter to collection
     *
     * @param int $entityId
     * @return \Magento\GiftRegistry\Model\Resource\Person\Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()->where('main_table.entity_id = ?', (int)$entityId);
        return $this;
    }
}
