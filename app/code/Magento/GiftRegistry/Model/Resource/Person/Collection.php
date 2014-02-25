<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\Person;

/**
 * Gift registry entity registrants collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Person', 'Magento\GiftRegistry\Model\Resource\Person');
    }

    /**
     * Apply entity filter to collection
     *
     * @param int $entityId
     * @return $this
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()->where('main_table.entity_id = ?', (int)$entityId);
        return $this;
    }
}
