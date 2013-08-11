<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Filter for system configuration. Removes restricted configuration options
 */
class Saas_Saas_Model_DisabledConfiguration_Structure_Converter_Filter
    extends Mage_Backend_Model_Config_Structure_Converter
{
    /**
     * @var Saas_Saas_Model_DisabledConfiguration_Config
     */
    private $_disabledConfig;

    /**
     * @param Mage_Backend_Model_Config_Structure_Mapper_Factory $mapperFactory
     * @param Saas_Saas_Model_DisabledConfiguration_Config $disabledConfig
     */
    public function __construct(Mage_Backend_Model_Config_Structure_Mapper_Factory $mapperFactory,
        Saas_Saas_Model_DisabledConfiguration_Config $disabledConfig
    ) {
        $this->_disabledConfig = $disabledConfig;
        parent::__construct($mapperFactory);
    }

    /**
     * Converts DOM document to array format and removes restricted options
     *
     * @param DOMNode $root
     * @return mixed
     */
    public function convert($root)
    {
        $result = parent::convert($root);

        if (isset($result['config']['system']['sections'])) {
            $this->_filterDisabledEntries($result['config']['system']['sections']);
        }

        return $result;
    }

    /**
     * Recursively remove disabled options
     *
     * @param array $entries
     * @param string $currentPath
     */
    protected function _filterDisabledEntries(array &$entries, $currentPath = '')
    {
        foreach ($entries as $entryId => &$entry) {
            $entryPath = $currentPath . $entryId;
            if ($this->_disabledConfig->isPathDisabled($entryPath)) {
                unset($entries[$entryId]);
            } else if (is_array($entry) && isset($entry['children'])) {
                $this->_filterDisabledEntries($entry['children'], $entryPath . '/');
            }
        }
        unset($entry);
    }
}
