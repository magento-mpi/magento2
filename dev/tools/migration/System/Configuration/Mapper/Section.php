<?php

class Tools_Migration_System_Configuration_Mapper_Section extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    protected $_groupMapper;

    public function __construct()
    {
        parent::__construct();

        $this->_allowedFieldNames = array(
            'label',
            'class',
            'resource',
            'header_css',
            'tab'
        );

        $this->_groupMapper = new Tools_Migration_System_Configuration_Mapper_Group();
    }

    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $sectionName => $sectionConfig) {
            $output[] = $this->_transformElement($sectionName, $sectionConfig, 'section');
        }
        return $output;
    }

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
