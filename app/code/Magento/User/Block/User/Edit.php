<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Block\User;

/**
 * User edit page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'user';
        $this->_blockGroup = 'Magento_User';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save User'));
        $this->buttonList->update('delete', 'label', __('Delete User'));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (!empty($objId)) {
            $this->addButton(
                'invalidate',
                array(
                    'label' => __('Invalidate Access Tokens'),
                    'class' => 'reset',
                    'onclick' => 'deleteConfirm(\'' . __(
                            json_encode(utf8_encode('Are you sure you want to invalidate this user\'s tokens?'), JSON_HEX_APOS)
                    ) . '\', \'' . $this->getInvalidateUrl() . '\')'
                )
            );
        }


    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('permissions_user')->getId()) {
            $username = $this->escapeHtml($this->_coreRegistry->registry('permissions_user')->getUsername());
            return __("Edit User '%1'", $username);
        } else {
            return __('New User');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('adminhtml/*/validate', array('_current' => true));
    }

    /**
     * Return invalidate url for edit form
     *
     * @return string
     */
    public function getInvalidateUrl()
    {
        return $this->getUrl('adminhtml/*/invalidatetoken', array('_current' => true));
    }
}
