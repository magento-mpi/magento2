<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward collection
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Resource\Reward;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Internal construcotr
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Reward\Model\Reward', 'Magento\Reward\Model\Resource\Reward');
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return \Magento\Reward\Model\Resource\Reward\Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where(is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId);
        return $this;
    }
}
