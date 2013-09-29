<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Menu source model for Layouts
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

class Layout implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\ConfigInterface
     */
    protected $_hierarchyConfig;

    /**
     * @param \Magento\VersionsCms\Model\Hierarchy\ConfigInterface $hierarchyConfig
     */
    public function __construct(\Magento\VersionsCms\Model\Hierarchy\ConfigInterface $hierarchyConfig)
    {
        $this->_hierarchyConfig = $hierarchyConfig;
    }

    /**
     * Return options for displaying Hierarchy Menu
     *
     * @param bool $withDefault Include or not default value
     * @return array
     */
    public function toOptionArray($withDefault = false)
    {
        $options = array();
        if ($withDefault) {
           $options[] = array('label' => __('Use default'), 'value' => '');
        }

        foreach ($this->_hierarchyConfig->getAllMenuLayouts() as $name => $info) {
            $options[] = array(
                'label' => $info['label'],
                'value' => $name
            );
        }

        return $options;
    }
}
