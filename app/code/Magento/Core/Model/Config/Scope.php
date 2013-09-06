<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Scope implements \Magento\Config\ScopeInterface
{
    /**
     * Current config scope
     *
     * @var string
     */
    protected $_currentScope;

    /**
     * List of all available config scopes
     *
     * @var array
     */
    protected $_availableScopes = array('global', 'adminhtml', 'frontend');

    /**
     * @param string $defaultScope
     */
    public function __construct($defaultScope = 'global')
    {
        $this->_currentScope = $defaultScope;
    }

    /**
     * Get current configuration scope identifier
     *
     * @return string
     */
    public function getCurrentScope()
    {
        return $this->_currentScope;
    }

    /**
     * Set current configuration scope
     *
     * @param string $scope
     */
    public function setCurrentScope($scope)
    {
        $this->_currentScope = $scope;
    }

    /**
     * Retrieve list of available config scopes
     *
     * @return array
     */
    public function getAllScopes()
    {
        return $this->_availableScopes;
    }
}
