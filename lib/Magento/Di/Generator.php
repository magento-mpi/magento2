<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Generator
{
    /**
     * @var Magento_Di_Generator_EntityAbstract
     */
    protected $_generator;

    /**
     * @var Magento_Autoload
     */
    protected $_autoloader;

    /**
     * @var array
     */
    protected $_generatedEntities = array(
        Magento_Di_Generator_Factory::ENTITY_TYPE
    );

    /**
     * @param Magento_Di_Generator_EntityAbstract $generator
     * @param Magento_Autoload $autoloader
     */
    public function __construct(
        Magento_Di_Generator_EntityAbstract $generator = null,
        Magento_Autoload $autoloader = null
    ) {
        $this->_generator = $generator;
        $this->_autoloader = $autoloader ?: Magento_Autoload::getInstance();
    }

    /**
     * @return array
     */
    public function getGeneratedEntities()
    {
        return $this->_generatedEntities;
    }

    /**
     * @param $className
     * @return bool
     * @throws Magento_Exception
     */
    public function generateClass($className)
    {
        // check if source class a generated entity
        $entity = null;
        $entityName = null;
        foreach ($this->_generatedEntities as $entityType) {
            $entitySuffix = ucfirst($entityType);
            // if $className string ends on $entitySuffix substring
            if (strrpos($className, $entitySuffix) === strlen($className) - strlen($entitySuffix)) {
                $entity = $entityType;
                $entityName = rtrim(substr($className, 0, -1 * strlen($entitySuffix)), '_');
                break;
            }
        }
        if (!$entity || !$entityName) {
            return false;
        }

        // check if file already exists
        if ($this->_autoloader->classExists($className)) {
            return false;
        }

        // generate class file
        if (!$this->_generator) {
            switch ($entity) {
                case Magento_Di_Generator_Factory::ENTITY_TYPE:
                    $this->_generator = new Magento_Di_Generator_Factory();
                    break;

                default:
                    throw new Magento_Exception('Unknown generation entity.');
                    break;
            }
        }
        $this->_generator->setSourceClassName($entityName);
        $this->_generator->setResultClassName($className);
        if (!$this->_generator->generate()) {
            $errors = $this->_generator->getErrors();
            throw new Magento_Exception(implode(' ', $errors));
        }

        return true;
    }
}
