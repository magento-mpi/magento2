<?php

class Tools_Migration_System_Configuration_Mapper_Tab extends Tools_Migration_System_Configuration_Mapper_Abstract
{
    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $tabName => $tabConfig) {
            $output[] = $this->_transformElement($tabName, $tabConfig, 'tab');
        }
        return $output;
    }
}
