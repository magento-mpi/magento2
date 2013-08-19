<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Revision selector
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Preview_Revision extends Magento_Adminhtml_Block_Template
{
    /**
     * Retrieve id of currently selected revision
     *
     * @return int
     */
    public function getRevisionId()
    {
        if (!$this->hasRevisionId()) {
            $this->setData('revision_id', (int)$this->getRequest()->getPost('preview_selected_revision'));
        }
        return $this->getData('revision_id');
    }

    /**
     * Prepare array with revisions sorted by versions
     *
     * @return array
     */
    public function getRevisions()
    {
        /* var $collection Enterprise_Cms_Model_Resource_Revision_Collection */
        $collection = Mage::getModel('Enterprise_Cms_Model_Page_Revision')->getCollection()
            ->addPageFilter($this->getRequest()->getParam('page_id'))
            ->joinVersions()
            ->addNumberSort()
            ->addVisibilityFilter(Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId(),
                Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel());

        $revisions = array();

        foreach ($collection->getItems() as $item) {
            if (isset($revisions[$item->getVersionId()])) {
                $revisions[$item->getVersionId()]['revisions'][] = $item;
            } else {
                $revisions[$item->getVersionId()] = array(
                    'revisions' => array($item),
                    'label' => ($item->getLabel() ? $item->getLabel() : __('N/A'))
                );
            }
        }
        krsort($revisions);
        reset($revisions);
        return $revisions;
    }
}
