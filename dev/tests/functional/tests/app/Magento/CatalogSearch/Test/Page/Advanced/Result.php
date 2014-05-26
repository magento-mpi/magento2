<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page\Advanced; 

use Magento\CatalogSearch\Test\Page\CatalogsearchResult;

/**
 * Class Result
 */
class Result extends CatalogsearchResult
{
    const MCA = 'catalogsearch/advanced/result';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['searchResultBlock'] = [
            'name' => 'searchResultBlock',
            'class' => 'Magento\CatalogSearch\Test\Block\Advanced\Result',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }

    /**
     * @return \Magento\CatalogSearch\Test\Block\Advanced\Result
     */
    public function getSearchResultBlock()
    {
        return $this->getBlockInstance('searchResultBlock');
    }
}
