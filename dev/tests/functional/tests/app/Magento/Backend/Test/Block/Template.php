<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Client;
use Mtf\Block\Block;

/**
 * Class Template
 * Backend abstract block
 *
 * @package Magento\Backend\Test\Block
 */
class Template extends Block
{
    /**
     * Wait until loader will be disappeared
     */
    public function waitLoader()
    {
        $this->waitForElementNotVisible('.loading-mask');
        $this->waitForElementNotVisible('.loader');
        $this->waitForElementNotVisible('.loading-old');
    }
}
