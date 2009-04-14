<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Permissions_Block_Catalog_Form_Renderer_Fieldset_Element extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Initialize block template
     */
    protected function _construct()
    {
        $this->setTemplate('enterprise/permissions/catalog/form/renderer/fieldset/element.phtml');
    }

    /**
     * Disable field in default value using case
     *
     * @return Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
     */
    public function checkFieldDisable()
    {
        if ($this->canDisplayUseDefault() && $this->usedDefault()) {
            $this->getElement()->setDisabled(true);
        }

        if( $this->isDisabled() && !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
            $this->getElement()->setDisabled(true);
        }

        return $this;
    }

    public function isDisabled()
    {
        $disabled = false;
        if ($this->getAttribute() && $this->getAttribute()->isScopeGlobal()) {
            $disabled = true;
        }

        $productWebsites = (array) ($this->getDataObject()) ? $this->getDataObject()->getWebsiteIds() : array();
        $userWebsites = Mage::helper('enterprise_permissions')->getAllowedWebsites();

        if( sizeof($productWebsites) > 0 && sizeof(array_diff($productWebsites, $userWebsites)) == 0 ) {
            $disabled = false;
        }

        return $disabled;
    }
}
