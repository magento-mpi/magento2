<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Item query
 *
 */
namespace Magento\Framework\Gdata\Gshopping;

class ItemQuery extends \Zend_Gdata_Query
{
    /**
     * The ID of an item
     *
     * @var string
     */
    protected $_id;

    /**
     * Content language code (ISO 639-1)
     *
     * @var string
     */
    protected $_language;

    /**
     * Target country code (ISO 3166)
     *
     * @var string
     */
    protected $_targetCountry;

    /**
     * @param string $value
     * @return \Zend_Gdata_Gbase_ItemQuery Provides a fluent interface
     */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Get item's ID
     *
     * @return string id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set language code
     *
     * @param string $language code
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Get language code
     *
     * @return string code
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set target country code
     *
     * @param string $targetCountry code
     * @return $this
     */
    public function setTargetCountry($targetCountry)
    {
        $this->_targetCountry = $targetCountry;
        return $this;
    }

    /**
     * Get target country code
     *
     * @return string code
     */
    public function getTargetCountry()
    {
        return $this->_targetCountry;
    }

    /**
     * Set default feed's URI
     *
     * @param string $uri URI
     * @return $this
     */
    public function setFeedUri($uri)
    {
        $this->_defaultFeedUri = $uri;
        return $this;
    }

    /**
     * Returns the query URL generated by this query instance.
     *
     * @return string The query URL for this instance.
     */
    public function getQueryUrl()
    {
        $uri = $this->_defaultFeedUri;
        $itemId = $this->_getItemId();

        return $itemId !== null ? "{$uri}/{$itemId}" : $uri . $this->getQueryString();
    }

    /**
     * Build item ID string (with country and language) for URL.
     *
     * @return null|string
     */
    protected function _getItemId()
    {
        return $this->_targetCountry !== null &&
            $this->_language !== null &&
            $this->_id !== null ? "online:{$this->_language}:{$this->_targetCountry}:{$this->_id}" : null;
    }
}
