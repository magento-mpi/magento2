<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Select_Extended
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * Get options for filter value
     *
     * @return array
     */
    protected function _getOptions()
    {
        $emptyOption = array('value' => null, 'label' => '');

        $optionGroups = $this->getColumn()->getOptionGroups();
        if ($optionGroups) {
            array_unshift($optionGroups, $emptyOption);
            return $optionGroups;
        }

        $colOptions = $this->getColumn()->getOptions();
        if (!empty($colOptions) && is_array($colOptions) ) {
            $options = array($emptyOption);
            foreach ($colOptions as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
            return $options;
        }
        return array();
    }

}
