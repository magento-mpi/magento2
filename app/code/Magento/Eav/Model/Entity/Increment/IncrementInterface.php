<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Increment;

interface IncrementInterface
{
    /**
     * Get next id
     *
     * @return mixed
     */
    public function getNextId();
}
