<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page UIMap class
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     *
     * @throws UnexpectedValueException
     */
    public function __construct($pageId, array &$pageContainer)
    {
        $this->_pageId = $pageId;

        if (array_key_exists('mca', $pageContainer)) {
            $this->_mca = $pageContainer['mca'];
        } else {
            throw new UnexpectedValueException("'MCA' parameter must be specified for '$pageId' page");
        }
        if (isset($pageContainer['click_xpath'])) {
            $this->_clickXpath = $pageContainer['click_xpath'];
        }
        if (array_key_exists('title', $pageContainer)) {
            $this->_title = $pageContainer['title'];
        } else {
            throw new UnexpectedValueException("'Title' parameter must be specified for '$pageId' page");
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

    /**
     * Get main buttons defined on the current page
     * @return mixed
     */
    public function getMainButtons()
    {
        if (isset($this->_elements['buttons'])) {
            return $this->_elements['buttons'];
        }
        return null;
    }
}
