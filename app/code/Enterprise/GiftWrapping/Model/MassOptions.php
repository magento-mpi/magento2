<?php
/**
 * Gift Wrapping statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftWrapping_Model_MassOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Backend Helper
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $helper
     */
    public function __construct(Enterprise_GiftWrapping_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '' => '',
            '1' => 'Enabled',
            '0' => 'Disabled',
        );
    }
}
