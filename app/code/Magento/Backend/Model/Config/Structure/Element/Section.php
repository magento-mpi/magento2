<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Section
    extends Magento_Backend_Model_Config_Structure_Element_CompositeAbstract
{
    /**
     * Authorization service
     *
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Model_App $application
     * @param Magento_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Backend_Model_Config_Structure_Element_Iterator $childrenIterator,
        Magento_AuthorizationInterface $authorization
    ) {
        parent::__construct($application, $childrenIterator);
        $this->_authorization = $authorization;
    }

    /**
     * Retrieve section header css
     *
     * @return string
     */
    public function getHeaderCss()
    {
        return isset($this->_data['header_css']) ? $this->_data['header_css'] : '';
    }

    /**
     * Check whether section is allowed for current user
     *
     * @return bool
     */
    public function isAllowed()
    {
        return isset($this->_data['resource']) ? $this->_authorization->isAllowed($this->_data['resource']) : false;
    }

    /**
     * Check whether element should be displayed
     *
     * @return bool
     */
    public function isVisible()
    {
        if (!$this->isAllowed()) {
            return false;
        }
        return parent::isVisible();
    }
}
