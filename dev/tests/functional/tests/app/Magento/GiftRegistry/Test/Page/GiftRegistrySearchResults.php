<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class GiftRegistrySearchResults
 */
class GiftRegistrySearchResults extends FrontendPage
{
    const MCA = 'giftregistry/search/results';

    protected $_blocks = [
        'searchResultsBlock' => [
            'name' => 'searchResultsBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Search\Results',
            'locator' => '.giftregistry.results',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Search\Results
     */
    public function getSearchResultsBlock()
    {
        return $this->getBlockInstance('searchResultsBlock');
    }
}
