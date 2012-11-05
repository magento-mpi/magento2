<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

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

    /**
     * @param Tools_Migration_System_Configuration_Mapper_Tab $tabMapper
     * @param Tools_Migration_System_Configuration_Mapper_Section $sectionMapper
     */
    public function __construct(Tools_Migration_System_Configuration_Mapper_Tab $tabMapper,
        Tools_Migration_System_Configuration_Mapper_Section $sectionMapper
    ) {
        $this->_tabMapper = $tabMapper;
        $this->_sectionMapper = $sectionMapper;
    }

    /**
     * Transform configuration
     *
     * @param array $config
     * @return array
     */
    public function transform(array $config)
    {
        $output = array();
        $output['comment'] = $config['comment'];

        $tabsConfig = isset($config['tabs']) ? $config['tabs'] : array();
        $sectionsConfig = isset($config['sections']) ? $config['sections'] : array();

        /** @var array $nodes  */
        $nodes = $this->_tabMapper->transform($tabsConfig);

        $transformedSections = $this->_sectionMapper->transform($sectionsConfig);

        $nodes = array_merge($nodes, $transformedSections);

        $output['nodes'] = $nodes;

        return $output;
    }
}
