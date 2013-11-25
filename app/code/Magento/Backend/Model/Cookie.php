<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend cookie model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model;

class Cookie extends \Magento\Core\Model\Cookie
{
    /**
     * Is https secure request
     * Use secure on adminhtml only
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->_getRequest()->isSecure();
    }
}
