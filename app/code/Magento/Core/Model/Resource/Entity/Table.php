<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Class describing db table resource entity
 *
 */
namespace Magento\Core\Model\Resource\Entity;

class Table extends \Magento\Core\Model\Resource\Entity\AbstractEntity
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
