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
 * System Configuration Dependencies Mapper
 */
class Mage_Backend_Model_Config_Structure_Mapper_Dependencies
    implements Mage_Backend_Model_Config_Structure_MapperInterface
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
                $sectionConfig = $this->_processConfig($sectionConfig);
            }
        }
        return $data;
    }

    protected function _processConfig($groupConfig)
    {
        $groupConfig = $this->_processDepends($groupConfig);

        if ($this->_hasValue('children', $groupConfig)) {
            foreach ($groupConfig['children'] as &$fieldConfig) {
                $fieldConfig = $this->_processConfig($fieldConfig);
            }
        }
        return $groupConfig;
    }

    protected function _processDepends($groupConfig)
    {
        if ($this->_hasValue('depends/fields', $groupConfig)) {
            foreach ($groupConfig['depends']['fields'] as &$field) {
                $dependPath = $this->_getDependPath($field, $groupConfig);
                $field['dependPath'] = $dependPath;
                $field['id'] = implode('/', $dependPath);
            }
        }
        return $groupConfig;
    }

    /**
     * Check value existence
     *
     * @param string $key
     * @param array $target
     * @return bool
     */
    protected function _hasValue($key, array $target)
    {
        $paths = explode('/', $key);
        foreach ($paths as $path) {
            if (array_key_exists($path, $target)) {
                $target = $target[$path];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Get depend path
     *
     * @param array $field
     * @param array $config
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _getDependPath($field, $config)
    {
        $dependPath = $field['id'];
        $elementPath = $config['path'] . '/' . $config['id'];

        $dependPathParts = explode('/', $dependPath);
        $elementPathParts = explode('/', $elementPath);
        $output = array();
        foreach ($dependPathParts as $index => $path) {

            if ($path === '*') {
                if (false == array_key_exists($index, $elementPathParts)) {
                    throw new InvalidArgumentException('Invalid relative depends structure');
                }
                $path = $elementPathParts[$index];
            }
            $output[$index] = $path;
        }
        return $output;
    }
}
