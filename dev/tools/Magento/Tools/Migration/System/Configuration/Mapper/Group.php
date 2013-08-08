<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_System_Configuration_Mapper_Group extends Magento_Tools_Migration_System_Configuration_Mapper_Abstract
{
    /**
     * @var Tools_Migration_System_Configuration_Mapper_Field
     */
    protected $_fieldMapper;

    /**
     * List of allowed field names for group
     * @var array
     */
    protected $_allowedFieldNames = array(
        'label',
        'frontend_model',
        'clone_fields',
        'clone_model',
        'fieldset_css',
        'help_url',
        'comment',
        'hide_in_single_store_mode',
        'expanded'
    );

    /**
     * @param Tools_Migration_System_Configuration_Mapper_Field $fieldMapper
     */
    public function __construct(Magento_Tools_Migration_System_Configuration_Mapper_Field $fieldMapper)
    {
        $this->_fieldMapper = $fieldMapper;
    }

    /**
     * Transform group configuration
     *
     * @param array $config
     * @return array
     */
    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $groupName => $groupConfig) {
            $output[] = $this->_transformElement($groupName, $groupConfig, 'group');
        }
        return $output;
    }

    /**
     * @param array $config
     * @param array $parentNode
     * @param array $element
     * @return array
     */
    protected function _transformSubConfig(array $config, $parentNode, $element)
    {
        if ($parentNode['name'] == 'fields') {
            $subConfig = $this->_fieldMapper->transform($config);
            if (null !== $subConfig) {
                $element['subConfig'] = $subConfig;
            }
        }
        return $element;
    }

}
