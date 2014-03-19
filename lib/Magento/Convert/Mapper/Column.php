<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Convert\Mapper;

use Magento\Convert\Container\AbstractContainer;

class Column extends AbstractContainer implements MapperInterface
{
    /**
     * @return $this
     */
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
        foreach ($data as $i => $row) {
            $newRow = array();
            foreach ($row as $field => $value) {
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
