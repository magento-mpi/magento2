<?php
/**
 * Store switcher
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Config;

use Mtf\Block\Block;
use Mtf\Client\Driver\Selenium\Element\SelectElement;

class Switcher extends Block
{
    /**
     * @var SelectElement
     */
    protected $_rootElement;

    /**
     * Select store
     *
     * @param string $groupName
     * @param string $name
     */
    public function selectStore($groupName, $name)
    {
        $this->_rootElement->setOptionGroupValue($groupName, $name);
    }
} 