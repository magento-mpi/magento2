<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Readiness block.
 */
class Readiness extends Block
{
    /**
     * 'Start Readiness Check' button.
     *
     * @var string
     */
    protected $readinessCheck = "//*[.='Start Readiness Check']";

    /**
     * 'Next' button.
     *
     * @var string
     */
    protected $next = "//*[.='Next']";

    /**
     * 'Completed!' message.
     *
     * @var string
     */
    protected $completedMessage = "//*[.='Completed!']";

    /**
     * PHP Version successful check.
     *
     * @var string
     */
    protected $phpVersionCheck = "//*[@id='php-version'][contains(.,'Your PHP version is correct')]";

    /**
     * PHP Extensions successful check.
     *
     * @var string
     */
    protected $phpExtensionCheck = "//*[@id='php-extensions'][contains(.,'You meet 2 out of 2 PHP extensions requirements.')]";

    /**
     * File Permission check.
     *
     * @var string
     */
    protected $filePermissionCheck = "//*[@id='php-permissions']";

    /**
     * Click on 'Start Readiness Check' button.
     *
     * @return void
     */
    public function clickReadinessCheck()
    {
        $this->_rootElement->find($this->readinessCheck, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Click on 'Next' button.
     *
     * @return void
     */
    public function clickNext()
    {
        $this->_rootElement->find($this->next, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get 'Completed!' message.
     *
     * @return string
     */
    public function getCompletedMessage()
    {
        return $this->_rootElement->find($this->completedMessage, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get File Permissions check result.
     *
     * @return string
     */
    public function getFilePermissionCheck()
    {
        return $this->_rootElement->find($this->filePermissionCheck, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get PHP Version check result.
     *
     * @return string
     */
    public function getPhpVersionCheck()
    {
        return $this->_rootElement->find($this->phpVersionCheck, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get PHP Extensions check result.
     *
     * @return string
     */
    public function getPhpExtensionsCheck()
    {
        return $this->_rootElement->find($this->phpExtensionCheck, Locator::SELECTOR_XPATH)->getText();
    }
}