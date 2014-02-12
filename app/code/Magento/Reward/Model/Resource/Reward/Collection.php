<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward;

/**
 * Reward collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Reward\Model\Reward', 'Magento\Reward\Model\Resource\Reward');
    }

    /**
     * Add filter by website id
     *
     * @param int|array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where(is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId);
        return $this;
    }
}
