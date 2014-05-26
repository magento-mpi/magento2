<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page; 

use Mtf\Page\FrontendPage; 

/**
 * Class Advanced
 */
class Advanced extends FrontendPage
{
    const MCA = 'catalogsearch/advanced';

    protected $_blocks = [
        'searchForm' => [
            'name' => 'searchForm',
            'class' => 'Magento\CatalogSearch\Test\Block\Form\Advanced',
            'locator' => '.form.search.advanced',
            'strategy' => 'css selector',
        ],
        'form' => [
            'name' => 'form',
            'class' => 'Magento\CatalogSearch\Test\Block\Advanced\Form',
            'locator' => '.form.search.advanced',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogSearch\Test\Block\Form\Advanced
     */
    public function getSearchForm()
    {
        return $this->getBlockInstance('searchForm');
    }

    /**
     * @return \Magento\CatalogSearch\Test\Block\Advanced\Form
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
