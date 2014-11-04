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


    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }

        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        $this->setInterval($filter);
        $priorFilters = $this->getPriorFilters($filterParams);
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }

        list($from, $to) = $filter;
        $this->getLayer()->getProductCollection()->addFieldToFilter('price', ['from' => $from, 'to' => $to]);


        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
        );

        return $this;
    }
}
