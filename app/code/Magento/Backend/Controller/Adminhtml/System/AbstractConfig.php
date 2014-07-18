<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Abstract Controller
 */
namespace Magento\Backend\Controller\Adminhtml\System;

abstract class AbstractConfig extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * @var ConfigSectionChecker
     */
    protected $_sectionChecker;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure,
        ConfigSectionChecker $sectionChecker
    ) {
        parent::__construct($context);
        $this->_configStructure = $configStructure;
        $this->_sectionChecker = $sectionChecker;

    }

    /**
     * Check if current section is found and is allowed
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $section = null;
        if (!$request->getParam('section')) {
            $section = $this->_configStructure->getFirstSection();
            $request->setParam('section', $section->getId());
        } else {
            if (!$this->isSectionAllowed($request->getParam('section'))) {
                $this->deniedAction();
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Proxy for checking section
     *
     * @param string $sectionId
     * @return bool
     */
    protected function isSectionAllowed($sectionId)
    {
        return $this->_sectionChecker->isSectionAllowed($sectionId);
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
     * @throws \Exception
     * @return bool
     * @throws NotFoundException
     */
    protected function _isSectionAllowed($sectionId)
    {
        try {
            if (false == $this->_configStructure->getElement($sectionId)->isAllowed()) {
                throw new \Exception('');
            }
            return true;
        } catch (\Zend_Acl_Exception $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            $this->deniedAction();
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
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
