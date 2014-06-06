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
 * Class AdvancedSearch
 */
class AdvancedSearch extends FrontendPage
{
    const MCA = 'catalogsearch/advanced';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\CatalogSearch\Test\Block\Advanced\Form',
            'locator' => '.form.search.advanced',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CatalogSearch\Test\Block\Advanced\Form
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
