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
 * CMS Hierarchy Menu source model for Chapter/Section options
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Menu_Chapter
{
    /**
     * Return options for Chapter/Section meta links
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('label' => __('No'), 'value' => ''),
            array('label' => __('Chapter'), 'value' => 'chapter'),
            array('label' => __('Section'), 'value' => 'section'),
            array('label' => __('Both'), 'value' => 'both'),
        );

        return $options;
    }
}
