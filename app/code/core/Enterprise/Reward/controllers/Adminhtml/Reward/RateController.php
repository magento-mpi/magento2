<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward admin rate controller
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Adminhtml_Reward_RateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if module functionality enabled
     *
     * @return Enterprise_Reward_Adminhtml_Reward_RateController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabled()
            && $this->getRequest()->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return $this;
    }

    /**
     * Initialize layout, breadcrumbs
     *
     * @return Enterprise_Reward_Adminhtml_Reward_RateController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_Reward::customer_reward')
            ->_addBreadcrumb(Mage::helper('Enterprise_Reward_Helper_Data')->__('Customers'),
                Mage::helper('Enterprise_Reward_Helper_Data')->__('Customers'))
            ->_addBreadcrumb(Mage::helper('Enterprise_Reward_Helper_Data')->__('Manage Reward Exchange Rates'),
                Mage::helper('Enterprise_Reward_Helper_Data')->__('Manage Reward Exchange Rates'));
        return $this;
    }

    /**
     * Initialize rate object
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    protected function _initRate()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Reward Exchange Rates'));

        $rateId = $this->getRequest()->getParam('rate_id', 0);
        $rate = Mage::getModel('Enterprise_Reward_Model_Reward_Rate');
        if ($rateId) {
            $rate->load($rateId);
        }
        Mage::register('current_reward_rate', $rate);
        return $rate;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Reward Exchange Rates'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New Action.
     * Forward to Edit Action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     */
    public function editAction()
    {
        $rate = $this->_initRate();

        $this->_title($rate->getRateId() ? sprintf("#%s", $rate->getRateId()) : $this->__('New Rate'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('rate');

        if ($data) {
            $rate = $this->_initRate();

            if ($this->getRequest()->getParam('rate_id') && ! $rate->getId()) {
                return $this->_redirect('*/*/');
            }

            $rate->addData($data);

            try {
                $rate->save();
                $this->_getSession()->addSuccess(Mage::helper('Enterprise_Reward_Helper_Data')->__('The rate has been saved.'));
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('Cannot save Rate.'));
                return $this->_redirect('*/*/edit', array('rate_id' => $rate->getId(), '_current' => true));
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Delete Action
     */
    public function deleteAction()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->_getSession()->addSuccess(Mage::helper('Enterprise_Reward_Helper_Data')->__('The rate has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Validate Action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object(array('error' => false));
        $post     = $this->getRequest()->getParam('rate');
        $message  = null;
        if (Mage::app()->isSingleStoreMode()) {
            $post['website_id'] = Mage::app()->getStore(true)->getWebsiteId();
        }

        if (!isset($post['customer_group_id'])
            || !isset($post['website_id'])
            || !isset($post['direction'])
            || !isset($post['value'])
            || !isset($post['equal_value'])) {
            $message = $this->__('Please enter all Rate information.');
        } elseif ($post['direction'] == Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
                  && ((int) $post['value'] <= 0 || (float) $post['equal_value'] <= 0)) {
              if ((int) $post['value'] <= 0) {
                  $message = $this->__('Please enter a positive integer number in the left rate field.');
              } else {
                  $message = $this->__('Please enter a positive number in the right rate field.');
              }
        } elseif ($post['direction'] == Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
                  && ((float) $post['value'] <= 0 || (int) $post['equal_value'] <= 0)) {
              if ((int) $post['equal_value'] <= 0) {
                  $message = $this->__('Please enter a positive integer number in the right rate field.');
              } else {
                  $message = $this->__('Please enter a positive number in the left rate field.');
              }
        } else {
            $rate       = $this->_initRate();
            $isRateUnique = $rate->getIsRateUniqueToCurrent(
                $post['website_id'],
                $post['customer_group_id'],
                $post['direction']
            );

            if (!$isRateUnique) {
                $message = $this->__('Rate with the same website, customer group and direction or covering rate already exists.');
            }
        }

        if ($message) {
            $this->_getSession()->addError($message);
            $this->_initLayoutMessages('Mage_Adminhtml_Model_Session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Enterprise_Reward::rates');
    }
}
