<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Block;

/**
 * Magento_User role block
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Role extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_controller = 'user_role';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_User';

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_headerText = __('Roles');
        $this->_addButtonLabel = __('Add New Role');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/editrole');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->getLayout()->getChildName($this->getNameInLayout(), 'grid')) {
            $this->setChild(
                'grid',
                $this->getLayout()->createBlock(
                    $this->_blockGroup . '\\Block\\Role\\Grid',
                    $this->_controller . '.grid')
                    ->setSaveParametersInSession(true)
            );
        }
        return \Magento\Backend\Block\Widget\Container::_prepareLayout();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('permissions_role_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
