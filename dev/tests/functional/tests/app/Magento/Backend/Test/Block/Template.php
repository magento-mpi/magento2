<?php
/**
 * {license_notice}
 *
 * @api
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Block\Block;

/**
 * Class Template
 * Backend abstract block
 */
class Template extends Block
{
    /**
     * Magento loader
     *
     * @var string
     */
    protected $loader = '[data-role="loader"]';

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loaderOld = '#loading-mask #loading_mask_loader';

    /**
     * Wait until loader will be disappeared
     *
     * @return void
     */
    public function waitLoader()
    {
        $this->waitForElementNotVisible($this->loader);
        $this->waitForElementNotVisible($this->loaderOld);
    }
}
