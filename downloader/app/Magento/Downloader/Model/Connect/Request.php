<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloader\Model\Connect;

/**
 * Class request
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Request extends \Magento\Downloader\Model
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->set('success_callback', 'clear_cache({success:parent.onSuccess, fail:parent.onFailure})');
        $this->set('failure_callback', 'parent.onFailure()');
    }
}
