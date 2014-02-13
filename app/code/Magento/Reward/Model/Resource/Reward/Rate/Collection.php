<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward\Rate;

/**
 * Reward rate collection
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
        $this->_init('Magento\Reward\Model\Reward\Rate', 'Magento\Reward\Model\Resource\Reward\Rate');
    }

    /**
     * Add filter by website id
     *
     * @param int|array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteId = array_merge((array)$websiteId, array(0));
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteId);
        return $this;
    }
}
