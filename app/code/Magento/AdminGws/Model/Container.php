<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

use Magento\AdminGws\Model\Role;
use \Magento\Backend\Block\Widget\ContainerInterface;

class Container
{
    /**
     * @var Role
     */
    protected $_role;

    /**
     * Initialize helper
     *
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->_role = $role;
    }

    /**
     * Remove customer attribute creation button from grid container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeAddNewCustomerAttributeButton(ContainerInterface $container)
    {
        if (!$this->_role->getIsAll()) {
            $container->removeButton('add');
        }
    }

    /**
     * Remove customer attribute deletion button from form container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeDeleteCustomerAttributeButton(ContainerInterface $container)
    {
        if ($this->_role->getIsAll()) {
            $container->removeButton('delete');
        }
    }


    /**
     * Remove product attribute add button
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeCatalogProductAttributeAddButton(ContainerInterface $container)
    {
        $container->removeButton('add');
    }

    /**
     * Remove product attribute save buttons
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeCatalogProductAttributeButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('save_and_edit_button');
        $container->removeButton('delete');
    }

    /**
     * Remove buttons for save and reindex on process edit page.
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeProcessEditButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('reindex');
    }

    /**
     * Remove control buttons for website-level roles on Manage Gift Card Accounts page
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeGiftCardAccountAddButton(ContainerInterface $container)
    {
        if (!$this->_role->getIsWebsiteLevel()) {
            $container->removeButton('add');
        }
    }

    /**
     * Remove control buttons for website-level roles on Gift Card Account Edit page
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeGiftCardAccountControlButtons(ContainerInterface $container)
    {
        if (!$this->_role->getIsWebsiteLevel()) {
            $container->removeButton('delete');
            $container->removeButton('save');
            $container->removeButton('send');
        }
    }

    /**
     * Remove buttons from TargetRule grid for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTargetRuleGridButtons(ContainerInterface $container)
    {
        $container->removeButton('add');
    }

    /**
     * Remove buttons from TargetRule Edit/View for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTargetRuleEditButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('save_and_continue_edit');
        $container->removeButton('delete');
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeCustomerGroupAddButton(ContainerInterface $container)
    {
        $container->removeButton('add');
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeCustomerGroupControlButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('delete');
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTaxRuleAddButton(ContainerInterface $container)
    {
        $container->removeButton('add');
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTaxRuleControlButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('save_and_continue');
        $container->removeButton('delete');
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTaxRateAddButton(ContainerInterface $container)
    {
        $container->removeButton('add');
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeTaxRateControlButtons(ContainerInterface $container)
    {
        $container->removeButton('save');
        $container->removeButton('delete');
    }

    /**
     * Remove button "Add RMA Attribute" for all GWS limited users
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function removeRmaAddAttributeButton(ContainerInterface $container)
    {
        $container->removeButton('add');
    }
}
