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
     * Loader selector
     *
     * @var string
     */
    protected $loader;

    /**
     * Loader selector
     *
     * @var string
     */
    protected $secondLoader;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->loader = '.loading-mask';
        $this->secondLoader = '.loading-old';
    }

    /**
     * Wait until loader will be disappeared
     */
    public function waitLoader()
    {
        $this->waitForElementNotVisible($this->loader);
        $this->waitForElementNotVisible($this->secondLoader);
    }
}
