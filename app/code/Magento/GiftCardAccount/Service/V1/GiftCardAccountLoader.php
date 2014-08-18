<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Service\V1;

use \Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GiftCardAccountLoader
 */
class GiftCardAccountLoader
{
    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCardFactory;

    /**
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardFactory
     */
    public function __construct(\Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardFactory)
    {
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * Load gift card account by code
     *
     * @param string $code
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function load($code)
    {
        $giftCardAccount = $this->giftCardFactory->create();
        $giftCardAccount->loadByCode($code);
        if (!$giftCardAccount->getId()) {
            throw NoSuchEntityException::singleField('code', $code);
        }
        return $giftCardAccount;
    }
}
