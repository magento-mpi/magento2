<?php
/**
 * Web API adminhtml user block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Block\Adminhtml;

class User extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Webapi';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_user';

    /**
     * Internal constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_headerText = __('API Users');
        $this->_updateButton('add', 'label', __('Add New API User'));
    }
}
