<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Downloadable\Setup\Product;

class Converter extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
{

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Convert CSV format row to array
     *
     * @param $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($row as $field => $value) {
            if ('category' == $field) {
                $data['category_ids'] = $this->getCategoryIds($this->getArrayValue($value));
                continue;
            }

            if ('qty' == $field) {
                $data['quantity_and_stock_status'] = ['qty' => $value];
                continue;
            }

            $options = $this->getAttributeOptionValueIdsPair($field);
            if ($options) {
                $value = $this->getArrayValue($value);
                $result = [];
                foreach ($value as $v) {
                    if (isset($options[$v])) {
                        $result[] = $options[$v];
                    }
                }
                $value = count($result) == 1 ? current($result) : $result;
            }
            $data[$field] = $value;
        }

        return $data;
    }

    /**
     * Get downloadable data from array
     *
     * @param $row
     * @param array $downloadableData
     * @return array
     */
    public function getDownloadableData($row, $downloadableData = array()) {
        $separatedData = $this->groupDownloadableData($row);
        $formattedData = $this->getFormattedData($separatedData);
        foreach ($formattedData as $dataType => $data) {
            $downloadableData[$dataType][] = $formattedData[$dataType];
        }

        return $downloadableData;
    }

    /**
     * Group downloadable data by link and sample array keys.
     *
     * @param $downloadableData
     * @return mixed
     */
    public function groupDownloadableData($downloadableData) {
        foreach ($downloadableData as $dataKey => $dataValue) {
            if (!empty($dataValue)) {
                if((preg_match('/^(link_item)/', $dataKey, $m)) && is_array($m)) {
                    $groupedData['link'][$dataKey] = $dataValue;
                } elseif ((preg_match('/^(sample_item)/', $dataKey, $m)) && $m >=1) {
                    $groupedData['sample'][$dataKey] = $dataValue;
                }
            }
            unset($dataKey);
            unset($dataValue);
        }

        return $groupedData;
    }

    /**
     * Will format data corresponding to the product sample data array values.
     *
     * @param $groupedData
     * @return mixed
     */
    public function getFormattedData($groupedData) {
        foreach ($groupedData as $dataType => $dataValue) {
            switch ($dataType) {
                case 'link':
                    $formattedData['link'] = $this->formatDownloadableLinkData($groupedData['link']);
                    break;
                case 'sample':
                    $formattedData['sample'] = $this->formatDownloadableSampleData($groupedData['sample']);
                    break;
            }
        }

        return $formattedData;

    }

    /**
     * Format downloadable sample data
     *
     * @param $sampleData
     * @return array
     */
    public function formatDownloadableSampleData($sampleData)
    {
        $sample =
            [
                'is_delete' => '',
                'sample_id' => '0',
                'title' => isset($sampleData['sample_item_title']) ? $sampleData['sample_item_title'] : '',
                'sample_url' => isset($sampleData['sample_item_url']) ? $sampleData['sample_item_url'] : '',
                'file' => '[]',
                'type' => 'url',
                'sort_order' => ''
            ];

        return $sample;
    }

    /**
     * Format downloadable link data
     *
     * @param $linkData
     * @return array
     */
    public function formatDownloadableLinkData($linkData)
    {
        $link =
            [
                'is_delete' => '',
                'link_id' => '0',
                'title' => isset($linkData['link_item_title']) ? $linkData['link_item_title'] : '',
                'price' => isset($linkData['link_item_price']) ? $linkData['link_item_price'] : '',
                'number_of_downloads' => '0',
                'is_shareable' => '2',
                'sample' =>
                    [
                        'file' => '[]',
                        'type' => 'url',
                        'url' => isset($linkData['link_item_sample_url']) ? $linkData['link_item_sample_url'] : ''
                    ],
                'file' => '[]',
                'type' => 'url',
                'link_url' => isset($linkData['link_item_url']) ? $linkData['link_item_url'] : '',
                'sort_order' => ''
            ];

        return $link;
    }

}
