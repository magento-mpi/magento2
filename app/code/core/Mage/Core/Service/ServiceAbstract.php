<?php
/**
 * Abstract Service Layer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_ServiceAbstract
{
    /**
     * Sets each value from data to entity Varien_Object using setter method.
     *
     * @param Varien_Object $entity
     * @param array $data
     */
    protected function _setDataUsingMethods($entity, array $data)
    {
        foreach ($data as $property => $value) {
            $entity->setDataUsingMethod($property, $value);
        }
    }
}