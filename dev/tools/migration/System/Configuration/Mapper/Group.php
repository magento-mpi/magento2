<?php

class Tools_Migration_System_Configuration_Mapper_Group extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    protected $_fieldMapper;

    public function __construct()
    {
        parent::__construct();
        $this->_allowedFieldNames = array(
            'label',
            'frontend_model',
            'clone_fields',
            'clone_model',
            'fieldset_css',
            'help_url',
            'expanded'
        );

        $this->_fieldMapper = new Tools_Migration_System_Configuration_Mapper_Field();
    }

    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $groupName => $groupConfig) {
            $output[] = $this->_transformElement($groupName, $groupConfig, 'group');
        }
        return $output;
    }

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
