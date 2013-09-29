<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System\Configuration;

class Mapper
{

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Mapper\Tab
     */
    protected $_tabMapper;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Mapper\Section
     */
    protected $_sectionMapper;

    /**
     * @param \Magento\Tools\Migration\System\Configuration\Mapper\Tab $tabMapper
     * @param \Magento\Tools\Migration\System\Configuration\Mapper\Section $sectionMapper
     */
    public function __construct(\Magento\Tools\Migration\System\Configuration\Mapper\Tab $tabMapper,
        \Magento\Tools\Migration\System\Configuration\Mapper\Section $sectionMapper
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
