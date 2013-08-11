<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Scope implements Magento_Config_ScopeInterface
{
    /**
     * Current config scope
     *
     * @var string
     */
    protected $_currentScope;

    /**
     * @param string $defaultScope
     */
    public function __construct($defaultScope = 'primary')
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
}
