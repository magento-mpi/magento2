<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer giftregistry edit block
 */
namespace Magento\GiftRegistry\Block\Customer\Edit;

class Registry extends  \Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit
{
    /**
     * Scope Selector 'registry/registrant'
     *
     * @var string
     */
    protected $_prefix = 'registry';

    /**
     * Return array of attributes groupped by group
     *
     * @return array
     */
    public function getGroupedRegistryAttributes()
    {
        return $this->getGroupedAttributes();
    }

    /**
     * Return privacy field selector (input type = select)
     *
     * @return sting
     */
    public function getIsPublicHtml()
    {
        $options[''] = __('Please Select');
        $options += $this->getEntity()->getOptionsIsPublic();
        $value = $this->getEntity()->getIsPublic();
        return $this->getSelectHtml($options, 'is_public', 'is_public', $value, 'required-entry');
    }

    /**
     * Return status field selector (input type = select)
     *
     * @return sting
     */
    public function getStatusHtml()
    {
        $options = $this->getEntity()->getOptionsStatus();
        $value = $this->getEntity()->getIsActive();
        return $this->getSelectHtml($options, 'is_active', 'is_active', $value, 'required-entry');
    }
}
