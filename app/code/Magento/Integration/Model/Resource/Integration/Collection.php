<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Resource\Integration;

/**
 * Integrations collection.
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Integration\Model\Integration', 'Magento\Integration\Model\Resource\Integration');
    }

    /**
     * Get unsecure enpoints
     *
     * @return $this
     */
    public function addUnsecureEndpointFilter()
    {
        return $this->addFieldToFilter('endpoint', ['like' => 'http:%']);
    }
}
