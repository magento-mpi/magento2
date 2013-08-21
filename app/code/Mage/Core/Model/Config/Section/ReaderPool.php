<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Config_Section_ReaderPool
{
    /**
     * @var array
     */
    protected $_readers = array();

    public function __construct(
        Mage_Core_Model_Config_Section_Reader_DefaultReader $default,
        Mage_Core_Model_Config_Section_Reader_Website $website
    ) {
        $this->_readers = array(
            'default' => $default,
            'website' => $website
        );
    }

    /**
     * @param string $scope
     * @return
     */
    public function getReader($scope)
    {
        return $this->_readers[$scope];
    }
} 
