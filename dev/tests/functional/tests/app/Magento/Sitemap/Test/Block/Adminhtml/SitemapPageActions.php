<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\FormPageActions;
use Mtf\Client\Element\Locator;

/**
 * Class SitemapPageActions
 * Backend sitemap form page actions
 */
class SitemapPageActions extends FormPageActions
{
    /**
     * "Save & Generate" button
     *
     * @var string
     */
    protected $saveAndGenerateButton = '#generate';

    /**
     * Click on "Save & Generate" button
     *
     * @return void
     */
    public function saveAndGenerate()
    {
        $this->_rootElement->find($this->saveAndGenerateButton)->click();
        $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_XPATH);
        $this->waitForElementNotVisible($this->loaderOld, Locator::SELECTOR_XPATH);
    }
}
