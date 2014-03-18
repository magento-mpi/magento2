<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Model\Source;

class Format extends \Magento\Model\AbstractModel implements \Magento\Option\ArrayInterface
{
    /**
     * Return list of gift card account code formats
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            \Magento\GiftCardAccount\Model\Pool::CODE_FORMAT_ALPHANUM
                => __('Alphanumeric'),
            \Magento\GiftCardAccount\Model\Pool::CODE_FORMAT_ALPHA
                => __('Alphabetical'),
            \Magento\GiftCardAccount\Model\Pool::CODE_FORMAT_NUM
                => __('Numeric'),
        );
    }

    /**
     * Return list of gift card account code formats as options array.
     * If $addEmpty true - add empty option
     *
     * @param boolean $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = false)
    {
        $result = array();

        if ($addEmpty) {
            $result[] = array('value' => '',
                              'label' => '');
        }

        foreach ($this->getOptions() as $value=>$label) {
            $result[] = array('value' => $value,
                              'label' => $label);
        }

        return $result;
    }
}
