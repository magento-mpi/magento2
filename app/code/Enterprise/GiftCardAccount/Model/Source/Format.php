<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Model_Source_Format extends Magento_Core_Model_Abstract
{
    /**
     * Return list of gift card account code formats
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            Enterprise_GiftCardAccount_Model_Pool::CODE_FORMAT_ALPHANUM
                => __('Alphanumeric'),
            Enterprise_GiftCardAccount_Model_Pool::CODE_FORMAT_ALPHA
                => __('Alphabetical'),
            Enterprise_GiftCardAccount_Model_Pool::CODE_FORMAT_NUM
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
