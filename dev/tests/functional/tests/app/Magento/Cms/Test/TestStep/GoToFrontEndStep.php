<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestStep;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Class GoToFrontEndStep
 * Opens frontend main page
 */
class GoToFrontEndStep implements TestStepInterface
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     */
    public function __construct(CmsIndex $cmsIndex)
    {
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Opens frontend main page and logout if customer is logged in
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
