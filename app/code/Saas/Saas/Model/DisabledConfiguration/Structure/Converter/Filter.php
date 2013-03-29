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
    public function convert(DOMNode $root)
    {
        $result = parent::convert($root);
        $systemConfig = $result['config']['system'];
        foreach ($systemConfig['sections'] as $sectionId => $section) {
            if ($this->_disabledConfig->isSectionDisabled($sectionId)) {
                unset($systemConfig['sections'][$sectionId]);
                continue;
            }
            foreach ($section['children'] as $groupId => $group) {
                if ($this->_disabledConfig->isGroupDisabled($sectionId . '/' . $groupId)) {
                    unset($systemConfig['sections'][$sectionId]['children'][$groupId]);
                    continue;
                }
                foreach (array_keys($group['children']) as $fieldId) {
                    if ($this->_disabledConfig->isFieldDisabled($sectionId . '/' . $groupId . '/' . $fieldId)) {
                        unset($systemConfig['sections'][$sectionId]['children'][$groupId]['children'][$fieldId]);
                    }
                }
            }
        }
        $result['config']['system'] = $systemConfig;
        return $result;
    }
}
