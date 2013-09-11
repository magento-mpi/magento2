<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config data collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Config\Data;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Config\Value', 'Magento\Core\Model\Resource\Config\Data');
    }

    /**
     * Add scope filter to collection
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $section
     * @return \Magento\Core\Model\Resource\Config\Data\Collection
     */
    public function addScopeFilter($scope, $scopeId, $section)
    {
        $this->addFieldToFilter('scope', $scope);
        $this->addFieldToFilter('scope_id', $scopeId);
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     *  Add path filter
     *
     * @param string $section
     * @return \Magento\Core\Model\Resource\Config\Data\Collection
     */
    public function addPathFilter($section)
    {
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     * Add value filter
     *
     * @param int|string $value
     * @return \Magento\Core\Model\Resource\Config\Data\Collection
     */
    public function addValueFilter($value)
    {
        $this->addFieldToFilter('value', array('like' => $value));
        return $this;
    }
}
