<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Oauth\Nonce;

use Magento\Oauth\OauthInterface;
use Magento\Oauth\ConsumerInterface;
use Magento\Oauth\NonceGeneratorInterface;

class Generator implements NonceGeneratorInterface
{
    /**
     * @var \Magento\Oauth\Helper\Oauth
     */
    protected $_oauthHelper;

    /**
     * @var  \Magento\Integration\Model\Oauth\Nonce\Factory
     */
    protected $_nonceFactory;

    /**
     * @var  int
     */
    protected $_nonceLength;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Possible time deviation for timestamp validation in seconds.
     */
    const TIME_DEVIATION = 600;

    /**
     * @param \Magento\Oauth\Helper\Oauth $oauthHelper
     * @param \Magento\Integration\Model\Oauth\Nonce\Factory $nonceFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param int $nonceLength - Length of the generated nonce
     */
    public function __construct(
        \Magento\Oauth\Helper\Oauth $oauthHelper,
        \Magento\Integration\Model\Oauth\Nonce\Factory $nonceFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $nonceLength = \Magento\Oauth\Helper\Oauth::LENGTH_NONCE
    ) {
        $this->_oauthHelper = $oauthHelper;
        $this->_nonceFactory = $nonceFactory;
        $this->_date = $date;
        $this->_nonceLength = $nonceLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNonce(ConsumerInterface $consumer = null)
    {
        return $this->_oauthHelper->generateRandomString($this->_nonceLength);
    }

    /**
     * {@inheritdoc}
     */
    public function generateTimestamp()
    {
        return $this->_date->timestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function validateNonce(ConsumerInterface $consumer, $nonce, $timestamp)
    {
        try {
            $timestamp = (int)$timestamp;
            if ($timestamp <= 0 || $timestamp > time() + self::TIME_DEVIATION) {
                throw new \Magento\Oauth\Exception(
                    __('Incorrect timestamp value in the oauth_timestamp parameter'),
                    OauthInterface::ERR_TIMESTAMP_REFUSED
                );
            }

            /** @var \Magento\Integration\Model\Oauth\Nonce $nonceObj */
            $nonceObj = $this->_nonceFactory->create()->loadByCompositeKey($nonce, $consumer->getId());

            if ($nonceObj->getNonce()) {
                throw new \Magento\Oauth\Exception(
                    __('The nonce is already being used by the consumer with ID %1', $consumer->getId()),
                    OauthInterface::ERR_NONCE_USED
                );
            }

            $nonceObj->setNonce($nonce)->setConsumerId($consumer->getId())->setTimestamp($timestamp)->save();
        } catch (\Magento\Oauth\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('An error occurred validating the nonce'));
        }
    }
}
