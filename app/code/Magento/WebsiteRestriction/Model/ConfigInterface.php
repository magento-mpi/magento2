<?php
/**
 * Website restrictions configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_WebsiteRestriction_Model_ConfigInterface
{
    /**
     * Get generic actions list
     *
     * @return array
     */
    public function getGenericActions();

    /**
     * Get register actions list
     *
     * @return array
     */
    public function getRegisterActions();

    /**
     * Define if restriction is active
     *
     * @param Magento_Core_Model_Store|string|int $store
     * @return bool
     */
    public function isRestrictionEnabled($store = null);

    /**
     * Get restriction mode
     *
     * @return int
     */
    public function getMode();

    /**
     * Get restriction HTTP status code
     *
     * @return int
     */
    public function getHTTPStatusCode();

    /**
     * Get restriction HTTP redirect code
     *
     * @return int
     */
    public function getHTTPRedirectCode();

    /**
     * Get restriction landing page code
     *
     * @return int
     */
    public function getLandingPageCode();
}
