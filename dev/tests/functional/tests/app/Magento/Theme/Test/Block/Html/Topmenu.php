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
namespace Magento\Theme\Test\Block\Html;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class Topmenu
 * Class top menu navigation block
 */
class Topmenu extends Block
{
    /**
     * Show all available parent categories
     *
     * @var string
     */
    protected $moreParentCategories = '.more.parent';

    /**
     * Top Elements of menu
     *
     * @var string
     */
    protected $navigationMenuItems = "/li";

    /**
     * Select category from top menu by name and click on it
     *
     * @param string $categoryName
     */
    public function selectCategoryByName($categoryName)
    {
        Functional::assertNotEmpty($categoryName, 'Category must be specified to select from top menu.');

        $moreCategoriesLink = $this->_rootElement->find($this->moreParentCategories, Locator::SELECTOR_CSS);
        /**
         * @TODO Eliminate excessive logic
         * Currently redundant actions are performed: "more categories" clicked even if category is already visible
         */
        if ($moreCategoriesLink->isVisible()) {
            $moreCategoriesLink->click();
            //TODO sleep should be removed after fix with category sliding
            sleep(2);
        }
        $categoryLink = $this->_rootElement->find('//a[span="' . $categoryName . '"]', Locator::SELECTOR_XPATH);
        $categoryLink->click();
    }

    /**
     * Check menu items count
     *
     * @param int $number
     * @return bool
     */
    public function assertNavigationMenuItemsCount($number)
    {
        $selector = $this->navigationMenuItems . '[' . ($number + 1) . ']';
        return !$this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
