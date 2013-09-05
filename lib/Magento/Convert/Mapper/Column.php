<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Convert column mapper
 *
 * @category   Magento
 * @package    Magento_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Convert\Mapper;

class Column extends \Magento\Convert\Container\AbstractContainer
    implements \Magento\Convert\Mapper\MapperInterface
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
