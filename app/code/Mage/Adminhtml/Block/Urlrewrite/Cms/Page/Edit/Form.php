<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for CMS page URL rewrites
 *
 * @method Mage_Cms_Model_Page getCmsPage()
 * @method Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form setCmsPage(Mage_Cms_Model_Page $model)
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form extends Mage_Adminhtml_Block_Urlrewrite_Edit_Form
{
    /**
     * Form post init
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form
     */
    protected function _formPostInit($form)
    {
        $cmsPage = $this->_getCmsPage();
        $form->setAction(
            Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'cms_page' => $cmsPage->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath Varien_Data_Form_Element_Abstract */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath Varien_Data_Form_Element_Abstract */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath Varien_Data_Form_Element_Abstract */
        $targetPath = $this->getForm()->getElement('target_path');

        $model = $this->_getModel();
        /** @var $cmsPageUrlrewrite Mage_Cms_Model_Page_Urlrewrite */
        $cmsPageUrlrewrite = Mage::getModel('Mage_Cms_Model_Page_Urlrewrite');
        if (!$model->getId()) {
            $idPath->setValue($cmsPageUrlrewrite->generateIdPath($cmsPage));

            $sessionData = $this->_getSessionData();
            if (!isset($sessionData['request_path'])) {
                $requestPath->setValue($cmsPageUrlrewrite->generateRequestPath($cmsPage));
            }
            $targetPath->setValue($cmsPageUrlrewrite->generateTargetPath($cmsPage));
            $disablePaths = true;
        } else {
            $cmsPageUrlrewrite->load($this->_getModel()->getId(), 'url_rewrite_id');
            $disablePaths = $cmsPageUrlrewrite->getId() > 0;
        }
        if ($disablePaths) {
            $idPath->setData('disabled', true);
            $targetPath->setData('disabled', true);
        }

        return $this;
    }

    /**
     * Get catalog entity associated stores
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getEntityStores()
    {
        $cmsPage = $this->_getCmsPage();
        $entityStores = array();

        // showing websites that only associated to CMS page
        if ($this->_getCmsPage()->getId()) {
            $entityStores = (array) $cmsPage->getResource()->lookupStoreIds($cmsPage->getId());
            $this->_requireStoresFilter = !in_array(0, $entityStores);

            if (!$entityStores) {
                throw new Mage_Core_Model_Store_Exception(
                    __('Chosen cms page does not associated with any website.')
                );
            }
        }

        return $entityStores;
    }

    /**
     * Get CMS page model instance
     *
     * @return Mage_Cms_Model_Page
     */
    protected function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage(Mage::getModel('Mage_Cms_Model_Page'));
        }
        return $this->getCmsPage();
    }
}
