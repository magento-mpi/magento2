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
 * Index admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated  Partially moved to module Backend
 */
class Mage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Global Search Action
     */
    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
        $items = array();

        if (!$this->_authorization->isAllowed('Mage_Adminhtml::global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Error'),
                'name' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Access Denied'),
                'description' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('You have not enough permissions to use this functionality.')
            );
            $totalCount = 1;
        } else {
            if (empty($searchModules)) {
                $items[] = array(
                    'id' => 'error',
                    'type' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Error'),
                    'name' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('No search modules were registered'),
                    'description' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please make sure that all global admin search modules are installed and activated.')
                );
                $totalCount = 1;
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                foreach ($searchModules->children() as $searchConfig) {

                    if ($searchConfig->acl && !$this->_authorization->isAllowed($searchConfig->acl)){
                        continue;
                    }

                    $className = $searchConfig->getClassName();

                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = new $className();
                    $results = $searchInstance->setStart($start)
                        ->setLimit($limit)
                        ->setQuery($query)
                        ->load()
                        ->getResults();
                    $items = array_merge_recursive($items, $results);
                }
                $totalCount = sizeof($items);
            }
        }

        $block = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Template')
            ->setTemplate('system/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
