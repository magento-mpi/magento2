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
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Menu_Layout implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Config
     */
    protected $_cmsConfig;

    /**
     * @param Magento_VersionsCms_Model_Hierarchy_Config $cmsConfig
     */
    public function __construct(Magento_VersionsCms_Model_Hierarchy_Config $cmsConfig)
    {
        $this->_cmsConfig = $cmsConfig;
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

        foreach ($this->_cmsConfig->getContextMenuLayouts() as $code => $info) {
            $options[] = array(
                'label' => $info->getLabel(),
                'value' => $code
            );
        }

        return $options;
    }
}
