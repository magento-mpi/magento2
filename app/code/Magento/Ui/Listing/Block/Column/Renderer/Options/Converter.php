<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer\Options;

class Converter
{
    /**
     * Convert data from tree format to flat format
     *
     * @param array $treeData
     * @return array
     */
    public function toFlatArray($treeData)
    {
        $options = array();
        if (is_array($treeData)) {
            foreach ($treeData as $item) {
                if (isset($item['value']) && isset($item['label'])) {
                    $options[$item['value']] = $item['label'];
                }
            }
        }
        return $options;
    }

    /**
     * Convert data from flat format to tree format
     *
     * @param array $flatData
     * @return array
     */
    public function toTreeArray($flatData)
    {
        $options = array();
        if (is_array($flatData)) {
            foreach ($flatData as $key => $item) {
                $options[] = array('value' => $key, 'label' => $item);
            }
        }
        return $options;
    }
}
