<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Autocomplete queries list
 */
namespace Magento\CatalogSearch\Block;

class Autocomplete extends \Magento\View\Element\AbstractBlock
{
    protected $_suggestData = null;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogsearchHelper;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\CatalogSearch\Helper\Data $catalogsearchHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\CatalogSearch\Helper\Data $catalogsearchHelper,
        array $data = array()
    ) {
        $this->_catalogsearchHelper = $catalogsearchHelper;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getSuggestData();
        if (!($count = count($suggestData))) {
            return $html;
        }

        $count--;

        $html = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $escapedTitle = $this->escapeHtml($item['title']);
            $html .=  '<li title="'.$escapedTitle.'" class="'.$item['row_class'].'">'
                . '<span class="amount">'.$item['num_of_results'].'</span>'.$escapedTitle.'</li>';
        }

        $html.= '</ul>';

        return $html;
    }

    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            $collection = $this->_catalogsearchHelper->getSuggestCollection();
            $query = $this->_catalogsearchHelper->getQueryText();
            $counter = 0;
            $data = array();
            foreach ($collection as $item) {
                $_data = array(
                    'title' => $item->getQueryText(),
                    'row_class' => (++$counter)%2?'odd':'even',
                    'num_of_results' => $item->getNumResults()
                );

                if ($item->getQueryText() == $query) {
                    array_unshift($data, $_data);
                }
                else {
                    $data[] = $_data;
                }
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }
/*
 *
*/
}
