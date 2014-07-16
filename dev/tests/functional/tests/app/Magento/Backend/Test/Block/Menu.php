<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Menu
 * Class top menu navigation block
 */
class Menu extends Block
{
    /**
     * Returns array of parent menu items present on dashboard menu
     *
     * @return array
     */
    public function getTopMenuItems()
    {
        $navigationMenu = $this->_rootElement;
        $menuItems = [];
        $counter = 1;
        $textSelector = 'a span';
        while ($navigationMenu->find('li.parent.level-0:nth-of-type(' . $counter . ')')->isVisible()) {
            $menuItems[] = strtolower(
                $navigationMenu->find('li.parent.level-0:nth-of-type(' . $counter . ')')
                    ->find($textSelector)
                    ->getText()
            );
            $counter++;
        }
        return $menuItems;
    }
}
