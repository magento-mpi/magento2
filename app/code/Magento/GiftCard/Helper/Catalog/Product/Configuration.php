<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Helper\Catalog\Product;

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Magento
 * @package    Magento_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Configuration extends \Magento\App\Helper\AbstractHelper
    implements \Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface
{
    /**
     * Catalog product configuration
     *
     * @var \Magento\Catalog\Helper\Product\Configuration|null
     */
    protected $_ctlgProdConfigur = null;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $ctlgProdConfigur
     * @param \Magento\Escaper $escaper
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $ctlgProdConfigur,
        \Magento\Escaper $escaper
    ) {
        $this->_ctlgProdConfigur = $ctlgProdConfigur;
        $this->_escaper = $escaper;
        parent::__construct($context);
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @param $code
     * @return string|false
     */
    public function prepareCustomOption(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item, $code)
    {
        $option = $item->getOptionByCode($code);
        if ($option) {
            $value = $option->getValue();
            if ($value) {
                return $this->_escaper->escapeHtml($value);
            }
        }
        return false;
    }

    /**
     * Get gift card option list
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getGiftcardOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $result = array();
        $value = $this->prepareCustomOption($item, 'giftcard_sender_name');
        if ($value) {
            $email = $this->prepareCustomOption($item, 'giftcard_sender_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label' => __('Gift Card Sender'),
                'value' => $value
            );
        }

        $value = $this->prepareCustomOption($item, 'giftcard_recipient_name');
        if ($value) {
            $email = $this->prepareCustomOption($item, 'giftcard_recipient_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label' => __('Gift Card Recipient'),
                'value' => $value
            );
        }

        $value = $this->prepareCustomOption($item, 'giftcard_message');
        if ($value) {
            $result[] = array(
                'label' => __('Gift Card Message'),
                'value' => $value
            );
        }

        return $result;
    }

    /**
     * Retrieves product options list
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @return array
     */
    public function getOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        return array_merge(
            $this->getGiftcardOptions($item),
            $this->_ctlgProdConfigur->getCustomOptions($item)
        );
    }
}
