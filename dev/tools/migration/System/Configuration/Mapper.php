<?php

class Tools_Migration_System_Configuration_Mapper
{

    /**
     * @var Tools_Migration_System_Configuration_Mapper_Tab
     */
    protected $_tabMapper;

    /**
     * @var Tools_Migration_System_Configuration_Mapper_Section
     */
    protected $_sectionMapper;

    public function __construct()
    {
        $this->_tabMapper = new Tools_Migration_System_Configuration_Mapper_Tab();
        $this->_sectionMapper = new Tools_Migration_System_Configuration_Mapper_Section();


    }

    public function transform(array $config)
    {
        $output = array();
        $output['comment'] = $config['comment'];

        $nodes = array();
        $config = $config['config'];

        $tabsConfig = isset($config['tabs']) ? $config['tabs'] : array();
        $sectionsConfig = isset($config['sections']) ? $config['sections'] : array();

        $transformedTabs = $this->_tabMapper->transform($tabsConfig);
        $nodes += $transformedTabs;

        $transformedSections = $this->_sectionMapper->transform($sectionsConfig);

        $nodes = array_merge($nodes, $transformedSections);

        $output['nodes'] = $nodes;

        return $output;
    }








}
