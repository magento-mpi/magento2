<?php
/**
 * Abstract entity service.
 *
 * Purpose: expose and manage business entities.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Type_Entity_Abstract extends Mage_Core_Service_Type_Abstract
{
    /**
     *
     * @param mixed $context
     * @return mixed
     */
    abstract public function create($context);

    /**
     *
     * @param mixed $context
     * @return mixed
     */
    abstract public function item($context);

    /**
     *
     * @param mixed $context
     * @return mixed
     */
    abstract public function items($context);

    /**
     *
     * @param mixed $context
     * @return mixed
     */
    abstract public function update($context);

    /**
     *
     * @param mixed $context
     * @return mixed
     */
    abstract public function delete($context);
}
