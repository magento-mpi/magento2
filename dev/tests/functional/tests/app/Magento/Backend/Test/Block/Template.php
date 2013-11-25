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

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Template
 * Backend abstract block
 *
 * @package Magento\Backend\Test\Block
 */
class Template extends Block
{
    /**
     * Magento loader
     *
     * @var string
     */
    protected $loader = '.loading_mask .loader';

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loaderOld = '#loading-mask #loading_mask_loader';

    /**
     * Wait until loader will be disappeared
     */
    public function waitLoader()
    {
        $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_CSS);
        $this->waitForElementNotVisible($this->loaderOld, Locator::SELECTOR_CSS);
    }
}
