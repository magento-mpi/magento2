<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Test\Block\Html;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Topmenu
 * Class top menu navigation block
 *
 * @package Magento\Page\Test\Block\Html
 */
class Topmenu extends Block
{
    /**
     * Show all available parent categories
     *
     * @var string
     */
    private $moreParentCategories;

    /**
     * Initialize for block elements
     */
    protected function _init()
    {
        //Elements
        $this->moreParentCategories = '.more.parent';
    }

    /**
     * Select category from top menu by name and click on it
     *
     * @param string $categoryName
     */
    public function selectCategoryByName($categoryName)
    {
        $moreCategoriesLink = $this->_rootElement->find($this->moreParentCategories, Locator::SELECTOR_CSS);
        /**
         * @TODO Eliminate excessive logic
         * Currently redundant actions are performed: "more categories" clicked even if category is already visible
         */
        if ($moreCategoriesLink->isVisible()) {
            $moreCategoriesLink->click();
            sleep(2); //TODO should be removed after fix with category sliding
        }
        $categoryLink = $this->_rootElement->find('//a[span="' . $categoryName . '"]', Locator::SELECTOR_XPATH);
        $categoryLink->click();
    }
}
