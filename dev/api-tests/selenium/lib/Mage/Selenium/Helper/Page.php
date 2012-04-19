<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page helper object
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Helper_Page extends Mage_Selenium_Helper_Abstract
{
    /**
     * Current page
     * @var string
     */
    protected $_currentPage = '';

    /**
     * Application helper instance
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Set Application helper instance to access application info
     *
     * @param Mage_Selenium_Helper_Application $applicationHelper Application helper instance
     *
     * @return Mage_Selenium_Helper_Abstract
     */
    public function setApplicationHelper(Mage_Selenium_Helper_Application $applicationHelper)
    {
        $this->_applicationHelper = $applicationHelper;
        return $this;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $area
     * @param string $page Page identifier
     *
     * @throws Mage_Selenium_Exception
     *
     * @return string
     */
    public function getPageUrl($area, $page)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception("ApplicationHelper hasn't been initialized yet");
        }

        $pageData = $this->_config->getUimapHelper()->getUimapPage($area, $page);

        if (empty($pageData)) {
            throw new Mage_Selenium_Exception('Page data is not defined');
        }
        $url = $this->_applicationHelper->getBaseUrl() . $pageData->getMca();
        return $url;
    }

    /**
     * Return xpath which we need to click to open page
     *
     * @param string $area
     * @param string $page Page identifier
     *
     * @throws Mage_Selenium_Exception
     *
     * @return string
     */
    public function getPageClickXpath($area, $page)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception("ApplicationHelper hasn't been initialized yet");
        }

        $pageData = $this->_config->getUimapHelper()->getUimapPage($area, $page);

        if (empty($pageData)) {
            throw new Mage_Selenium_Exception('Page data is not defined');
        }

        return $pageData->getClickXpath();
    }

    /**
     * Convert page MCA to page ID
     *
     * @param string area
     * @param string Page's mca
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @throws Mage_Selenium_Exception
     *
     * @return string Page identifier
     */
    public function getPageByMca($area, $mca, $paramsDecorator = null)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception("ApplicationHelper hasn't been initialized yet");
        }

        return $this->_config
            ->getUimapHelper()
            ->getUimapPageByMca($area, $mca, $paramsDecorator);
    }

    /**
     * Returns PageID of current page
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Set PageID
     *
     * @param string $page
     */
    public function setCurrentPage($page)
    {
        $this->_currentPage = $page;
    }
}
