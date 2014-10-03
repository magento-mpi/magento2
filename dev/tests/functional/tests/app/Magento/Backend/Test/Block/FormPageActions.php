<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Client\Element\Locator;

/**
 * Class FormPageActions
 * Form page actions block
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class FormPageActions extends PageActions
{
    /**
     * "Back" button
     *
     * @var string
     */
    protected $backButton = '#back';

    /**
     * "Reset" button
     *
     * @var string
     */
    protected $resetButton = '#reset';

    /**
     * "Save and Continue Edit" button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue';

    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * "Delete" button
     *
     * @var string
     */
    protected $deleteButton = '#delete';

    /**
     * "Publish" button
     *
     * @var string
     */
    protected $publishButton = '#publish_button';

    /**
     * Magento loader
     *
     * @var string
     */
    protected $loader = '//ancestor::body/div[@data-role="loader"]';

    /**
     * Magento varienLoader.js loader
     *
     * @var string
     */
    protected $loaderOld = '//ancestor::body/div[@id="loading-mask"]';

    /**
     * Click on "Back" button
     */
    public function back()
    {
        $this->_rootElement->find($this->backButton)->click();
    }

    /**
     * Click on "Reset" button
     */
    public function reset()
    {
        $this->_rootElement->find($this->resetButton)->click();
    }

    /**
     * Click on "Save and Continue Edit" button
     */
    public function saveAndContinue()
    {
        $this->_rootElement->find($this->saveAndContinueButton)->click();
        $this->waitForElementNotVisible('.popup popup-loading');
        $this->waitForElementNotVisible('.loader');
    }

    /**
     * Click on "Save" button
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
        $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_XPATH);
        $this->waitForElementNotVisible($this->loaderOld, Locator::SELECTOR_XPATH);
    }

    /**
     * Click on "Delete" button
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteButton)->click();
        $this->_rootElement->acceptAlert();
    }

    /**
     * Check 'Delete' button availability
     *
     * @return bool
     */
    public function checkDeleteButton()
    {
        return $this->_rootElement->find($this->deleteButton)->isVisible();
    }

    /**
     * Click 'Publish' button
     *
     * @return void
     */
    public function publish()
    {
        $this->_rootElement->find($this->publishButton)->click();
    }
}
