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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enterprise cms page observer
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Observer
{
    /**
     * Limit displayed fields on cms page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function filterFieldsOnPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        /** @var $baseFieldset Varien_Data_Form_Element_Fieldset */
        $baseFieldset = $form->getElement('base_fieldset');

        /*
         * Making is_active as disabled if user does not have publish permission
         */
        if (!Mage::getSingleton('enterprise_cms/config')->isCurrentUserCanPublishRevision()) {
            $element = $baseFieldset->getElements()->searchById('is_active');
            if ($element) {
                $element->setDisabled(true);
            }
        }
//        $elementsUnderRevisionControl = Mage::getSingleton('enterprise_cms/config')
//            ->getPageRevisionControledAttributes();

//        if (Mage::registry('cms_page')->getHideRevisionedAttributes()) {
//            foreach ($baseFieldset->getElements() as $element) {
//                if (in_array($element->getId(), $elementsUnderRevisionControl)) {
//                    $baseFieldset->removeField($element->getId());
//                }
//            }
//        } else if (Mage::registry('cms_page')->getHideNotRevisionedAttributes()) {
//            /*
//             * Removing fields that are not under
//             * revision control except those which are hidden
//             */
//            foreach ($baseFieldset->getElements() as $element) {
//                if (!in_array($element->getId(), $elementsUnderRevisionControl)
//                        && $element->getType() != 'hidden') {
//                    $baseFieldset->removeField($element->getId());
//                }
//            }
//        }

//        return $this;
    }
}
