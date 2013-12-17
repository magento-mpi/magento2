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
        $moreCategoriesLink = $this->_rootElement->find($this->moreParentCategories);
        $category = $this->_rootElement->find('//a[span="' . $categoryName . '"]', Locator::SELECTOR_XPATH);
        if (!$category->isVisible() && $moreCategoriesLink->isVisible()) {
            $moreCategoriesLink->click();
            $this->_rootElement->waitUntil(
                function () use ($category) {
                    return $category->isVisible() ? true : null;
                }
            );
        }
        $category->click();
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
