<?php
/**
 * oAuth consumer interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Oauth_Model_ConsumerInterface
{
    /**
     * Load consumer by key.
     *
     * @param string $key
     * @return Magento_Oauth_Model_ConsumerInterface
     */
    public function loadByKey($key);

    /**
     * Get consumer ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Get consumer key.
     *
     * @return string
     */
    public function getSecret();

    /**
     * Get consumer callback URL.
     *
     * @return string
     */
    public function getCallBackUrl();
}
