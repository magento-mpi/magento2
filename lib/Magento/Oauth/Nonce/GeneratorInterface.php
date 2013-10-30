<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth\Nonce;

use Magento\Oauth\ConsumerInterface;

interface GeneratorInterface
{
    /**
     * Generate a new Nonce for the consumer (if consumer is specified).
     *
     * @param ConsumerInterface $consumer
     * @return mixed - The generated nonce value.
     */
    public function generateNonce(ConsumerInterface $consumer = null);

    /**
     * Generate a timestamp.
     *
     * @return int
     */
    public function generateTimestamp();

    /**
     * Validate the specified Nonce and persist it with the consumer and timestamp.
     *
     * @param ConsumerInterface $consumer
     * @param mixed $nonce - The nonce value.
     * @param int $timestamp - The 'oauth_timestamp' value.
     * @throws \Magento\Oauth\Exception - Exceptions are thrown for validation errors.
     */
    public function validateNonce(ConsumerInterface $consumer, $nonce, $timestamp);
}
