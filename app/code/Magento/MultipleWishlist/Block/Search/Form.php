<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple Wishlist search form
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Block_Search_Form extends Magento_Core_Block_Template
{
    /**
     * Posted form data
     *
     * @var array|null
     */
    protected $_formData = null;

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return __('Wish List Search');
    }

    /**
     * Retrieve submitted param by key
     *
     * @param string $key
     * @return mixed
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = $this->getRequest()->getParam('params');
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        return $this->escapeHtml($this->_formData[$key]);
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('wishlist/search/results');
    }
}
