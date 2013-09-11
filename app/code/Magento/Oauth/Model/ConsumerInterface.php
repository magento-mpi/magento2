<?php
/**
 * oAuth consumer interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Oauth\Model;

interface ConsumerInterface
{
    /**
     * Load consumer by key.
     *
     * @param string $key
     * @return \Magento\Oauth\Model\ConsumerInterface
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
