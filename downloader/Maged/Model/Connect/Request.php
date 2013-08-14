<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class request
 *
 * @category   Mage
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Maged_Model_Connect_Request extends Maged_Model
{
    protected function _construct()
    {
        parent::_construct();
        $this->set('success_callback', 'clear_cache({success:parent.onSuccess, fail:parent.onFailure})');
        $this->set('failure_callback', 'parent.onFailure()');
    }
}
