<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Tools_Migration_System_Configuration_Mapper_Tab
    extends Magento_Tools_Migration_System_Configuration_Mapper_Abstract
{
    /**
     * Attribute maps
     * oldName => newName
     * @var array
     */
    protected $_attributeMaps = array(
        'sort_order' => 'sortOrder',
        'frontend_type' => 'type',
        'class' => 'class'
    );

    /**
     * List of allowed filed names for tab
     *
     * @var array
     */
    protected $_allowedFieldNames = array(
        'label',
    );

    /**
     * Transform tabs configuration
     *
     * @param array $config
     * @return array
     */
    public function transform(array $config)
    {
        $output = array();
        foreach ($config as $tabName => $tabConfig) {
            $output[] = $this->_transformElement($tabName, $tabConfig, 'tab');
        }
        return $output;
    }

    /**
     * Transform sub configuration
     *
     * @param array $config
     * @param array $parentNode
     * @param array $element
     * @return array
     */
    protected function _transformSubConfig(array $config, $parentNode, $element)
    {
        return $element;
    }
}
