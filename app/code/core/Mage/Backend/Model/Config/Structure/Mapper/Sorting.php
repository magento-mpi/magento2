<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Sorting Mapper
 */
class Mage_Backend_Model_Config_Structure_Mapper_Sorting extends Mage_Backend_Model_Config_Structure_MapperAbstract
{
    /**
     * Apply map
     *
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        foreach ($data['config']['system'] as &$element) {
            $element = $this->_processConfig($element);
        }
        return $data;
    }

    protected function _processConfig($data)
    {
        foreach ($data as &$item) {
            if ($this->_hasValue('children', $item)) {
                $item['children'] = $this->_processConfig($item['children']);
            }
        }
        uasort($data, array($this, '_cmp'));
        return $data;
    }


    /**
     * Compare elements
     *
     * @param array $elementA
     * @param array $elementB
     * @return int
     */
    protected function _cmp($elementA, $elementB)
    {
        $sortIndexA = $this->_hasValue('sortOrder', $elementA) ? intval($elementA['sortOrder']) : 0;
        $sortIndexB = $this->_hasValue('sortOrder', $elementB) ? intval($elementB['sortOrder']) : 0;

        if ($sortIndexA == $sortIndexB) {
            return 0;
        }

        return ($sortIndexA < $sortIndexB) ? -1 : 1;
    }
}
