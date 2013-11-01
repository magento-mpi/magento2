<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth;

/**
 * Interface ConsumerInterface
 *
 * This interface exposes minimal consumer functionality needed by the Oauth library.
 *
 * @package Magento\Oauth
 */
interface ConsumerInterface
{
    /**
     * Validate consumer data (e.g. Key and Secret length).
     *
     * @return bool - True if the consumer data is valid.
     * @throws \Magento\Core\Exception|\Exception - Throws exception for validation errors.
     */
    public function validate();

    /**
     * Get the consumer Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get consumer key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Get consumer secret.
     *
     * @return string
     */
    public function getSecret();

    /**
     * Get consumer callback Url.
     *
     * @return string
     */
    public function getCallbackUrl();
}
