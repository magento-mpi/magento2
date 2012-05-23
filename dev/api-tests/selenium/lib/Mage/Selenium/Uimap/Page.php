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
 * Page UIMap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_Page extends Mage_Selenium_Uimap_Abstract
{
    /**
     * Page Identificator from UIMaps
     *
     * @var string
     */
    protected $_pageId = '';

    /**
     * Page MCA, part of the page URL after baseURL
     *
     * @var string
     */
    protected $_mca = '';

    /**
     * click_xpath defined in UIMaps
     *
     * @var string
     */
    protected $_clickXpath = '';

    /**
     * Page title
     *
     * @var string
     */
    protected $_title = '';

    /**
     * Page class constructor
     *
     * @param string $pageId Page ID
     * @param array $pageContainer Array of data, which contains in specific page
     */
    public function  __construct($pageId, array &$pageContainer)
    {
        $this->_pageId = $pageId;

        if (isset($pageContainer['mca'])) {
            $this->_mca = $pageContainer['mca'];
        }
        if (isset($pageContainer['click_xpath'])) {
            $this->_clickXpath = $pageContainer['click_xpath'];
        }
        if (isset($pageContainer['title'])) {
            $this->_title = $pageContainer['title'];
        }
        if (isset($pageContainer['uimap'])) {
            $this->_parseContainerArray($pageContainer['uimap']);
        }
    }

    /**
     * Get page ID
     *
     * @return string ID of the page
     */
    public function getPageId()
    {
        return $this->_pageId;
    }

    /**
     * Get page mca
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null (by default = null)
     *
     * @return string
     */
    public function getMca($paramsDecorator = null)
    {
        return $this->_applyParamsToString($this->_mca, $paramsDecorator);
    }

    /**
     * Get page click xpath
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null (by default = null)
     *
     * @return string
     */
    public function getClickXpath($paramsDecorator = null)
    {
        return $this->_applyParamsToString($this->_clickXpath, $paramsDecorator);
    }

    /**
     * Get page title
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null
     *
     * @return string Title of the page
     */
    public function getTitle($paramsDecorator = null)
    {
        return $this->_applyParamsToString($this->_title, $paramsDecorator);
    }

    /**
     * Get the main form defined on the current page
     *
     * @return Mage_Selenium_Uimap_Form
     */
    public function getMainForm()
    {
        return $this->_elements['form'];
    }
}
