<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Downloadable\Setup\Product;

/**
 * Class Converter
 */
class Converter extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
{
    /**
     * Get downloadable data from array
     *
     * @param array $row
     * @param array $downloadableData
     * @return array
     */
    public function getDownloadableData($row, $downloadableData = array())
    {
        $separatedData = $this->groupDownloadableData($row);
        $formattedData = $this->getFormattedData($separatedData);
        foreach (array_keys($formattedData) as $dataType) {
            $downloadableData[$dataType][] = $formattedData[$dataType];
        }

        return $downloadableData;
    }

    /**
     * Group downloadable data by link and sample array keys.
     *
     * @param array $downloadableData
     * @return array
     */
    public function groupDownloadableData($downloadableData)
    {
        $groupedData = [];
        foreach ($downloadableData as $dataKey => $dataValue) {
            if (!empty($dataValue)) {
                if ((preg_match('/^(link_item)/', $dataKey, $matches)) && is_array($matches)) {
                    $groupedData['link'][$dataKey] = $dataValue;
                } elseif ((preg_match('/^(sample_item)/', $dataKey, $matches)) && $matches >=1) {
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
     * @param array $groupedData
     * @return array
     */
    public function getFormattedData($groupedData)
    {
        $formattedData = [];
        foreach (array_keys($groupedData) as $dataType) {
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
     * @param array $sampleData
     * @return array
     */
    public function formatDownloadableSampleData($sampleData)
    {
        $sampleItems = array(
            'sample_item_title',
            'sample_item_url'
        );
        foreach ($sampleItems as $csvRow) {
            $sampleData[$csvRow] = isset($sampleData[$csvRow]) ? $sampleData[$csvRow] : '';
        }
        $sample =
            [
                'is_delete' => '',
                'sample_id' => '0',
                'title' => $sampleData['sample_item_title'],
                'sample_url' => $sampleData['sample_item_url'],
                'file' => '[]',
                'type' => 'url',
                'sort_order' => ''
            ];

        return $sample;
    }

    /**
     * Format downloadable link data
     *
     * @param array $linkData
     * @return array
     */
    public function formatDownloadableLinkData($linkData)
    {
        $linkItems = array(
            'link_item_title',
            'link_item_price',
            'link_item_sample_url',
            'link_item_url'
        );
        foreach ($linkItems as $csvRow) {
            $linkData[$csvRow] = isset($linkData[$csvRow]) ? $linkData[$csvRow] : '';
        }

        $link =
            [
                'is_delete' => '',
                'link_id' => '0',
                'title' => $linkData['link_item_title'],
                'price' => $linkData['link_item_price'],
                'number_of_downloads' => '0',
                'is_shareable' => '2',
                'sample' =>
                    [
                        'file' => '[]',
                        'type' => 'url',
                        'url' => $linkData['link_item_sample_url']
                    ],
                'file' => '[]',
                'type' => 'url',
                'link_url' => $linkData['link_item_url'],
                'sort_order' => ''
            ];

        return $link;
    }
}
