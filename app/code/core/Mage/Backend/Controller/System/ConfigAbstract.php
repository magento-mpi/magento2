<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Abstract Controller
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Backend_Controller_System_ConfigAbstract extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Authorization model
     *
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * Constructor
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Authorization $authorization,
        Mage_Backend_Model_Config_Structure $configStructure,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $invokeArgs);

        $this->_authorization = $authorization;
        $this->_configStructure = $configStructure;
    }

    /**
     * Controller pre-dispatch method
     * Check if current section is found and is allowed
     *
     * @return Mage_Backend_Controller_ActionAbstract
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $section = null;
        if (!$this->getRequest()->getParam('section')) {
            $section = $this->_configStructure->getFirstSection();
            $this->getRequest()->setParam('section', $section->getId());
        } else {
            $this->_isSectionAllowed($this->getRequest()->getParam('section'));
        }
        return $this;
    }

    /**
     * Check is allow modify system configuration
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Adminhtml::config');
    }

    /**
     * Check if specified section allowed in ACL
     *
     * Will forward to deniedAction(), if not allowed.
     *
     * @param string $sectionId
     * @throws Exception
     * @return bool
     */
    protected function _isSectionAllowed($sectionId)
    {
        try {
            if (false == $this->_configStructure->getElement($sectionId)->isAllowed()) {
                throw new Exception('');
            }
            return true;
        } catch (Zend_Acl_Exception $e) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (Exception $e) {
            $this->deniedAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
    }
}
