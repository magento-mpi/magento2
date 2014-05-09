<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

/**
 * CMS Hierarchy Menu source model for Chapter/Section options
 *
 */
class Chapter implements \Magento\Framework\Option\ArrayInterface
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
            array('label' => __('Both'), 'value' => 'both')
        );

        return $options;
    }
}
