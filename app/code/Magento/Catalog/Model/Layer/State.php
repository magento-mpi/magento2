<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layered navigation state model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Layer_State extends Magento_Object
{
    /**
     * Add filter item to layer state
     *
     * @param   Magento_Catalog_Model_Layer_Filter_Item $filter
     * @return  Magento_Catalog_Model_Layer_State
     */
    public function addFilter($filter)
    {
        $filters = $this->getFilters();
        $filters[] = $filter;
        $this->setFilters($filters);
        return $this;
    }

    /**
     * Set layer state filter items
     *
     * @param   array $filters
     * @return  Magento_Catalog_Model_Layer_State
     * @throws Magento_Core_Exception
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            throw new Magento_Core_Exception(__('The filters must be an array.'));
        }
        $this->setData('filters', $filters);
        return $this;
    }

    /**
     * Get applied to layer filter items
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = $this->getData('filters');
        if (is_null($filters)) {
            $filters = array();
            $this->setData('filters', $filters);
        }
        return $filters;
    }
}
