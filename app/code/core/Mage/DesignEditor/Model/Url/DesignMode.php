<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design mode design editor url model
 */
class Mage_DesignEditor_Model_Url_DesignMode extends Mage_Core_Model_Url
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
    }

    /**
     * Retrieve route path
     *
     * @param array $routeParams
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRoutePath($routeParams = array())
    {
        return '#';
    }
}
