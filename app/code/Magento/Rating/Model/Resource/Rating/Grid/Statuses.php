<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid statuses option array
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Resource_Rating_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Rating_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Rating_Helper_Data $ratingHelper
     */
    public function __construct(Magento_Rating_Helper_Data $ratingHelper)
    {
        $this->_helper = $ratingHelper;
    }

    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Active'),
            '0' => $this->_helper->__('Inactive')
        );
    }
}
