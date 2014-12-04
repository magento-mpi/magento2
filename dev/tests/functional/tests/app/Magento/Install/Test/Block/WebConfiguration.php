<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

/**
 * Web configuration block.
 */
class WebConfiguration extends ConfigurationForm
{
    /**
     * 'Next' button.
     *
     * @var string
     */
    protected $next = "[ng-click*='next']";

    /**
     * 'Advanced Options' locator.
     *
     * @var string
     */
    protected $advancedOptions = "[ng-click*='advanced']";

    /**
     * Click on 'Next' button.
     *
     * @return void
     */
    public function clickNext()
    {
        $this->_rootElement->find($this->next)->click();
    }

    /**
     * Click on 'Advanced Options' button.
     *
     * @return void
     */
    public function clickAdvancedOptions()
    {
        $this->_rootElement->find($this->advancedOptions)->click();
    }
}
