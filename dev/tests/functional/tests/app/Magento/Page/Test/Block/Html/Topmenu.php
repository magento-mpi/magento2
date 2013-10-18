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
 * Class top menu navigation block
 *
 * @package Magento\Page\Test\Block\Html
 */
class Topmenu extends Block
{
    /**
     * Select category from top menu by name and click on it
     *
     * @param string $categoryName
     */
    public function selectCategoryByName($categoryName)
    {
        $categoryLink = $this->_rootElement->find('//a[span="'.$categoryName.'"]', Locator::SELECTOR_XPATH);
        if ($categoryLink->isVisible()) {
            $categoryLink->click();
        }
    }
}
