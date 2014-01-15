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
namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\App\Action\NotFoundException;

abstract class AbstractConfig extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Backend\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure
    ) {
        parent::__construct($context);
        $this->_configStructure = $configStructure;
    }

    /**
     * Check if current section is found and is allowed
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        $section = null;
        if (!$request->getParam('section')) {
            $section = $this->_configStructure->getFirstSection();
            $request->setParam('section', $section->getId());
        } else {
            $this->_isSectionAllowed($request->getParam('section'));
        }
        return parent::dispatch($request);
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
