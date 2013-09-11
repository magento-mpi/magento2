<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for CMS page URL rewrites
 *
 * @method \Magento\Cms\Model\Page getCmsPage()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit\Form setCmsPage(\Magento\Cms\Model\Page $model)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit;

class Form extends \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
{
    /**
     * Form post init
     *
     * @param \Magento\Data\Form $form
     * @return \Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit\Form
     */
    protected function _formPostInit($form)
    {
        $cmsPage = $this->_getCmsPage();
        $form->setAction(
            \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'cms_page' => $cmsPage->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath \Magento\Data\Form\Element\AbstractElement */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath \Magento\Data\Form\Element\AbstractElement */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath \Magento\Data\Form\Element\AbstractElement */
        $targetPath = $this->getForm()->getElement('target_path');

        $model = $this->_getModel();
        /** @var $cmsPageUrlrewrite \Magento\Cms\Model\Page\Urlrewrite */
        $cmsPageUrlrewrite = \Mage::getModel('\Magento\Cms\Model\Page\Urlrewrite');
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
     * @throws \Magento\Core\Model\Store\Exception
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
                throw new \Magento\Core\Model\Store\Exception(
                    __('Chosen cms page does not associated with any website.')
                );
            }
        }

        return $entityStores;
    }

    /**
     * Get CMS page model instance
     *
     * @return \Magento\Cms\Model\Page
     */
    protected function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage(\Mage::getModel('\Magento\Cms\Model\Page'));
        }
        return $this->getCmsPage();
    }
}
