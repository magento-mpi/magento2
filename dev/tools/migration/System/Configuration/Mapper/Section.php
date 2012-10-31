<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_System_Configuration_Mapper_Section extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    /**
     * @var Tools_Migration_System_Configuration_Mapper_Group
     */
    protected $_groupMapper;

    /**
     * List of allowed filed names for section
     *
     * @var array
     */
    protected $_allowedFieldNames = array(
        'label',
        'class',
        'resource',
        'header_css',
        'tab'
    );

    /**
     * @param Tools_Migration_System_Configuration_Mapper_Group $groupMapper
     */
    public function __construct(Tools_Migration_System_Configuration_Mapper_Group $groupMapper)
    {
        $this->_groupMapper = $groupMapper;
    }

    /**
     * Transform section config
     *
     * @param array $config
     * @return array
     */
    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $sectionName => $sectionConfig) {
            $output[] = $this->_transformElement($sectionName, $sectionConfig, 'section');
        }
        return $output;
    }

    /**
     * Transform section sub configuration
     *
     * @param array $config
     * @param array $parentNode
     * @param array $element
     * @return array
     */
    protected function _transformSubConfig(array $config, $parentNode, $element)
    {
        if ($parentNode['name'] == 'groups') {
            $subConfig = $this->_groupMapper->transform($config);
            if (null !== $subConfig) {
                $element['subConfig'] = $subConfig;
            }
        }
        return $element;
    }
}
