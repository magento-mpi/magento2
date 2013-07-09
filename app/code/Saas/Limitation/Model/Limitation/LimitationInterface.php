<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Saas_Limitation_Model_Limitation_LimitationInterface
{
    /**
     * Retrieve maximum number of entities allowed in the system
     *
     * @return int
     */
    public function getThreshold();

    /**
     * Retrieve total number of entities in the system
     *
     * @return int
     */
    public function getTotalCount();
}
