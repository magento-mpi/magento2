<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * System Configuration Path Mapper
 */
namespace Magento\Backend\Model\Config\Structure\Mapper;

class Path extends \Magento\Backend\Model\Config\Structure\AbstractMapper
{
    /**
     * Apply map
     *
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        if ($this->_hasValue('config/system/sections', $data)) {
            foreach ($data['config']['system']['sections'] as &$sectionConfig) {
                if ($this->_hasValue('children', $sectionConfig)) {
                    foreach ($sectionConfig['children'] as &$groupConfig) {
                        $groupConfig = $this->_processConfig($groupConfig, $sectionConfig);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Process configuration
     *
     * @param array $elementConfig
     * @param array $parentConfig
     * @return array
     */
    protected function _processConfig(array $elementConfig, array $parentConfig)
    {
        $parentPath = $this->_hasValue('path', $parentConfig) ? $parentConfig['path'] . '/' : '';
        $parentPath .= $parentConfig['id'];
        $elementConfig['path'] = $parentPath;

        if ($this->_hasValue('children', $elementConfig)) {
            foreach ($elementConfig['children'] as &$subConfig) {
                $subConfig = $this->_processConfig($subConfig, $elementConfig);
            }
        }

        return $elementConfig;
    }
}
