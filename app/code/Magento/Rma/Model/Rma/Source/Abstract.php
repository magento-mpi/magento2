<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item attribute source abstract model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Rma_Model_Rma_Source_Abstract extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Getter for all available options
     *
     * @param bool $withLabels
     * @return array
     */
    public function getAllOptions($withLabels = true)
    {
        $values = $this->_getAvailableValues();
        if ($withLabels) {
            $result = array();
            foreach ($values as $item) {
                $result[] = array(
                    'label' => $this->getItemLabel($item),
                    'value' => $item
                );
            }
            return $result;

        }
        return $values;
    }

    /**
     * Getter for all available options for filter in grid
     *
     * @return array
     */
    public function getAllOptionsForGrid()
    {
        $values = $this->_getAvailableValues();
        $result = array();
        foreach ($values as $item) {
            $result[$item] = $this->getItemLabel($item);
        }
        return $result;
    }

    /**
     * Get available keys for entities
     *
     * @return array
     */
    abstract protected function _getAvailableValues();

    /**
     * Get label based on the code
     *
     * @param string $item
     * @return string
     */
    abstract public function getItemLabel($item);
}
