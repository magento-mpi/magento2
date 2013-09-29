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
 * Source model for websites, including "All" option
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Source;

class Website implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_store;

    /**
     * @param \Magento\Core\Model\System\Store $store
     */
    public function __construct(\Magento\Core\Model\System\Store $store)
    {
        $this->_store = $store;
    }

    /**
     * Prepare and return array of website ids and their names
     *
     * @param bool $withAll Whether to prepend "All websites" option on not
     * @return array
     */
    public function toOptionArray($withAll = true)
    {
        $websites = $this->_store->getWebsiteOptionHash();
        if ($withAll) {
            $websites = array(0 => __('All Websites'))
                      + $websites;
        }
        return $websites;
    }
}
