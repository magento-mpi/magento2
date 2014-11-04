<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

/**
 * Layer price filter based on Search API
 *
 */
class Price extends \Magento\Catalog\Model\Layer\Filter\Price
{
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $productCollection = $this->getLayer()->getProductCollection();
        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());

        $data = [];
        if (count($facets) > 1) { // two range minimum
            foreach ($facets as $key => $aggregation) {
                $count = $aggregation['count'];
                list($from, $to) = explode('_', $key);
                if ($from == '*') {
                    $from = '';
                }
                if ($to== '*') {
                    $to= '';
                }
                $label = $this->_renderRangeLabel(
                    empty($from) ? 0 : $from * $this->getCurrencyRate(),
                    empty($to) ? $to: $to* $this->getCurrencyRate()
                );
                $value =  $from . '-' . $to . $this->_getAdditionalRequestData();


                $data[] = [
                    'label' => $label,
                    'value' => $value,
                    'count' => $count,
                    'from' => $from,
                    'to' => $to
                ];
            }
        }

        return $data;
    }
}
