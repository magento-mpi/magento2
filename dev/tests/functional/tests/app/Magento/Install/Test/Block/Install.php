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
 * Install block.
 */
class Install extends Block
{
    /**
     * 'Install Now' button.
     *
     * @var string
     */
    protected $installNow = "//*[.='Install Now']";

    /**
     * Admin info block.
     *
     * @var string
     */
    protected $adminInfo = "//*[@id='admin-info']";

    /**
     * Database info block.
     *
     * @var string
     */
    protected $dbInfo = "//*[@id='db-info']";

    /**
     * 'Launch Magento Admin' button.
     *
     * @var string
     */
    protected $launchAdmin = "//*[.='Launch Magento Admin']";

    /**
     * Click on 'Install Now' button.
     *
     * @return void
     */
    public function clickInstallNow()
    {
        $this->_rootElement->find($this->installNow, Locator::SELECTOR_XPATH)->click();
        $this->waitForElementVisible($this->launchAdmin, Locator::SELECTOR_XPATH);
    }

    /**
     * Get admin info.
     *
     * @return string
     */
    public function getAdminInfo()
    {
        return $this->_rootElement->find($this->adminInfo, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get database info.
     *
     * @return string
     */
    public function getDbInfo()
    {
        return $this->_rootElement->find($this->dbInfo, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Click on 'Launch Magento Admin' button.
     *
     * @return void
     */
    public function clickLaunchAdmin()
    {
        $this->_rootElement->find($this->launchAdmin, Locator::SELECTOR_XPATH)->click();
    }
}