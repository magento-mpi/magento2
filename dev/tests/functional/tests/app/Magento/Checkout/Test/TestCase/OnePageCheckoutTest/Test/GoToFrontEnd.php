<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class GoToFrontEnd
 * Opens frontend main page
 */
class GoToFrontEnd implements StepInterface
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CmsIndex $cmsIndex
     */
    public function __construct(CmsIndex $cmsIndex)
    {
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Run step that opens frontend main page and logout if customer is logged in
     *
     * @return void
     */
    public function run()
    {
        $this->cmsIndex->open();
        if ($this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink('Log Out');
        }
    }
}
