<?php

class Tools_Migration_System_Configuration_Mapper_Tab extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    public function __construct()
    {
        $this->_attributeMaps = array(
            'sort_order' => 'sortOrder',
            'frontend_type' => 'type',
            'class' => 'class'
        );

        $this->_allowedFieldNames = array(
            'label',
        );

    }

    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $tabName => $tabConfig) {
            $output[] = $this->_transformElement($tabName, $tabConfig, 'tab');
        }
        return $output;
    }

    protected function _transformSubConfig(array $config, $parentNode, $element)
    {
        return $element;
    }
}
