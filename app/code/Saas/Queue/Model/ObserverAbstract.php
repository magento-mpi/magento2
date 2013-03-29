<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Queue
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for all job dispatchers
 * TODO: implement email sending
 */
abstract class Saas_Queue_Model_ObserverAbstract
{
    /**
     * Check whether worker instance should notify by email
     *
     * @return bool
     */
    abstract public function useInEmailNotification();
}
