<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Abstract Controller
 */
abstract class Magento_Backend_Controller_System_ConfigAbstract extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Backend_Model_Config_Structure $configStructure
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Backend_Model_Config_Structure $configStructure
    ) {
        parent::__construct($context);
        $this->_configStructure = $configStructure;
    }

    /**
     * Controller pre-dispatch method
     * Check if current section is found and is allowed
     *
     * @return Magento_Backend_Controller_ActionAbstract
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
        return $this->_authorization->isAllowed('Magento_Adminhtml::config');
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

    /**
     * Save state of configuration field sets
     *
     * @param array $configState
     * @return bool
     */
    protected function _saveState($configState = array())
    {
        $adminUser = $this->_auth->getUser();
        if (is_array($configState)) {
            $extra = $adminUser->getExtra();
            if (!is_array($extra)) {
                $extra = array();
            }
            if (!isset($extra['configState'])) {
                $extra['configState'] = array();
            }
            foreach ($configState as $fieldset => $state) {
                $extra['configState'][$fieldset] = $state;
            }
            $adminUser->saveExtra($extra);
        }
        return true;
    }
}
