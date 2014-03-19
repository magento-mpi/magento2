<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model\Resource\Entity;


/**
 * Class describing db table resource entity
 *
 */
class Table extends \Magento\Model\Resource\Entity\AbstractEntity
{
    /**
     * Get table
     *
     * @return String
     */
    function getTable()
    {
        return $this->getConfig('table');
    }
}
