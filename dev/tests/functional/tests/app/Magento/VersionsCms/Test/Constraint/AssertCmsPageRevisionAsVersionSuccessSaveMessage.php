<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Constraint;

use Magento\VersionsCms\Test\Page\Adminhtml\CmsVersionEdit;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\ObjectManager;

/**
 * Class AssertCmsPageRevisionAsVersionSuccessSaveMessage
 * Assert that after save CMS page revision as version save successful message appears
 */
class AssertCmsPageRevisionAsVersionSuccessSaveMessage extends AssertCmsPageNewVersionSuccessSaveMessage
{
    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Browser $browser
     */
    public function __construct(ObjectManager $objectManager, Browser $browser)
    {
        parent::__construct($objectManager);
        $this->browser = $browser;
    }

    /**
     * Assert that after save CMS page revision as version save successful message appears
     *
     * @param CmsVersionEdit $cmsVersionEdit
     * @return void
     */
    public function processAssert(CmsVersionEdit $cmsVersionEdit)
    {
        $this->browser->selectWindow();
        parent::processAssert($cmsVersionEdit);
        $this->browser->closeWindow();
        $this->browser->selectWindow();
    }
}
