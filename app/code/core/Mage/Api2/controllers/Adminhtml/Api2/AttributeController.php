<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 attribute controller
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Adminhtml_Api2_AttributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show user types grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('REST Attributes'));

        $this->loadLayout()->_setActiveMenu('Mage_Api2::system_api_rest_attributes');

        $this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'))
            ->_addBreadcrumb($this->__('REST Attributes'), $this->__('REST Attributes'))
            ->_addBreadcrumb($this->__('Attributes'), $this->__('Attributes'));

        $this->renderLayout();
    }

    /**
     * Edit role
     */
    public function editAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Api2::system_api_rest_attributes');

        $type = $this->getRequest()->getParam('type');

        $userTypes = Mage_Api2_Model_Auth_User::getUserTypes();
        if (!isset($userTypes[$type])) {
            $this->_getSession()->addError($this->__('User type "%s" not found.', $type));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($this->__('System'))
            ->_title($this->__('Web Services'))
            ->_title($this->__('REST ACL Attributes'));

        $title = $this->__('Edit %s ACL attribute rules', $userTypes[$type]);
        $this->_title($title);
        $this->_addBreadcrumb($title, $title);

        $this->renderLayout();
    }

    /**
     * Save role
     */
    public function saveAction()
    {
        $request = $this->getRequest();

        $type = $request->getParam('type');

        if (!$type) {
            $this->_getSession()->addError(
                $this->__('User type "%s" no longer exists', $type));
            $this->_redirect('*/*/');
            return;
        }

        /** @var $session Mage_Adminhtml_Model_Session */
        $session = $this->_getSession();

        try {
            /** @var $ruleTree Mage_Api2_Model_Acl_Global_Rule_Tree */
            $ruleTree = Mage::getSingleton(
                'Mage_Api2_Model_Acl_Global_Rule_Tree',
                array('options' => array('type' => Mage_Api2_Model_Acl_Global_Rule_Tree::TYPE_ATTRIBUTE))
            );

            /** @var $attribute Mage_Api2_Model_Acl_Filter_Attribute */
            $attribute = Mage::getModel('Mage_Api2_Model_Acl_Filter_Attribute');

            /** @var $collection Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection */
            $collection = $attribute->getCollection();
            $collection->addFilterByUserType($type);

            /** @var $model Mage_Api2_Model_Acl_Filter_Attribute */
            foreach ($collection as $model) {
                $model->delete();
            }

            foreach ($ruleTree->getPostResources() as $resourceId => $operations) {
                if (Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL === $resourceId) {
                    $attribute->setUserType($type)
                        ->setResourceId($resourceId)
                        ->save();
                } else {
                    foreach ($operations as $operation => $attributes) {
                        $attribute->setId(null)
                            ->isObjectNew(true);

                        $attribute->setUserType($type)
                            ->setResourceId($resourceId)
                            ->setOperation($operation)
                            ->setAllowedAttributes(implode(',', array_keys($attributes)))
                            ->save();
                    }
                }
            }

            $session->addSuccess($this->__('The attribute rules were saved.'));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while saving attribute rules.'));
        }

        $this->_redirect('*/*/edit', array('type' => $type));
    }
}
