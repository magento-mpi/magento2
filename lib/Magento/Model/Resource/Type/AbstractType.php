<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model\Resource\Type;

abstract class AbstractType
{
    /**
     * Name
     *
     * @var String
     */
    protected $_name = '';

    /**
     * Entity class
     *
     * @var String
     */
    protected $_entityClass = 'Magento\Model\Resource\Entity\AbstractEntity';

    /**
     * Retrieve entity type
     *
     * @return String
     */
    public function getEntityClass()
    {
        return $this->_entityClass;
    }

    /**
     * Set name
     *
     * @param String $name
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Retrieve name
     *
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }
}
