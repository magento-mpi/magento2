<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure;

interface ElementInterface
{
    /**
     * Set element data
     *
     * @param array $data
     * @param string $scope
     * @return void
     */
    public function setData(array $data, $scope);

    /**
     * Retrieve element configuration
     *
     * @return array
     */
    public function getData();

    /**
     * Retrieve element id
     *
     * @return string
     */
    public function getId();

    /**
     * Retrieve element label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Check whether element is visible
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Retrieve arbitrary element attribute
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key);
}

