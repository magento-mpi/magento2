<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Convert column mapper
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Convert_Mapper_Column extends Magento_Convert_Container_Abstract
    implements Magento_Convert_Mapper_Interface
{
    public function map()
    {
        $data = $this->getData();
        $this->validateDataGrid($data);
        if ($this->getVars() && is_array($this->getVars())) {
            $attributesToSelect = $this->getVars();
        } else {
            $attributesToSelect = array();
        }
        $onlySpecified = (bool)$this->getVar('_only_specified')===true;
        $mappedData = array();
        foreach ($data as $i=>$row) {
            $newRow = array();
            foreach ($row as $field=>$value) {
                if (!$onlySpecified || $onlySpecified && isset($attributesToSelect[$field])) {
                    $newRow[$this->getVar($field, $field)] = $value;
                }
            }
            $mappedData[$i] = $newRow;
        }
        $this->setData($mappedData);
        return $this;
    }
}