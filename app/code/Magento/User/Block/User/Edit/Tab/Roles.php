<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Block\User\Edit\Tab;

class Roles extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var \Magento\User\Model\Resource\Role\CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\User\Model\Resource\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\User\Model\Resource\Role\CollectionFactory $userRolesFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_userRolesFactory = $userRolesFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setTitle(__('User Roles Information'));
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->getSelectedRoles();
            if (empty($userRoles)) {
                $userRoles = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('role_id', array('in'=>$userRoles));
            } else {
                if ($userRoles) {
                    $this->getCollection()->addFieldToFilter('role_id', array('nin'=>$userRoles));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_userRolesFactory->create();
        $collection->setRolesFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('assigned_user_role', array(
            'header_css_class' => 'a-center',
            'header'    => __('Assigned'),
            'type'      => 'radio',
            'html_name' => 'roles[]',
            'values'    => $this->getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'role_id'
        ));

        $this->addColumn('role_name', array(
            'header'    => __('Role'),
            'index'     => 'role_name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $userPermissions = $this->_coreRegistry->registry('permissions_user');
        return $this->getUrl('*/*/rolesGrid', array('user_id' => $userPermissions->getUserId()));
    }

    public function getSelectedRoles($json=false)
    {
        if ( $this->getRequest()->getParam('user_roles') != "" ) {
            return $this->getRequest()->getParam('user_roles');
        }
        /* @var $user \Magento\User\Model\User */
        $user = $this->_coreRegistry->registry('permissions_user');
        //checking if we have this data and we
        //don't need load it through resource model
        if ($user->hasData('roles')) {
            $uRoles = $user->getData('roles');
        } else {
            $uRoles = $user->getRoles();
        }

        if ($json) {
            $jsonRoles = Array();
            foreach ($uRoles as $urid) {
                $jsonRoles[$urid] = 0;
            }
            return $this->_jsonEncoder->encode((object)$jsonRoles);
        } else {
            return $uRoles;
        }
    }

}
