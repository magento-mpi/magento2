<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UrlRewrite Options source model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Source_Urlrewrite_Options
{
    const TEMPORARY = 'R';
    const PERMANENT = 'RP';

    /**
     * @var array|null
     */
    protected $_options = null;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                ''              => Mage::helper('Magento_Adminhtml_Helper_Data')->__('No'),
                self::TEMPORARY => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Temporary (302)'),
                self::PERMANENT => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Permanent (301)')
            );
        }
        return $this->_options;
    }

    /**
     * Get options list (redirects only)
     *
     * @return array
     */
    public function getRedirectOptions()
    {
        return array(self::TEMPORARY, self::PERMANENT);
    }
}
