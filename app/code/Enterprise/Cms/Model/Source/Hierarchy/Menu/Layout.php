<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Menu source model for Layouts
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Menu_Layout
{
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

        foreach (Mage::getSingleton('Enterprise_Cms_Model_Hierarchy_Config')->getContextMenuLayouts() as $code => $info) {
            $options[] = array(
                'label' => $info->getLabel(),
                'value' => $code
            );
        }

        return $options;
    }
}
