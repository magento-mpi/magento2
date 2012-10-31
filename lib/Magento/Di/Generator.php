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
        Magento_Di_Generator_Factory::DI_GENERATOR_CODE => 'Factory'
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
     * @param $className
     * @return bool
     * @throws Magento_Exception
     */
    public function generateClass($className)
    {
        // check if source class a generated entity
        $entity = null;
        $entityName = null;
        foreach ($this->_generatedEntities as $entityCode => $entityPattern) {
            // if $className string ends on $entityPattern substring
            if (strrpos($className, $entityPattern) === strlen($className) - strlen($entityPattern)) {
                $entity = $entityCode;
                $entityName = rtrim(substr($className, 0, -1 * strlen($entityPattern)), '_');
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
                case Magento_Di_Generator_Factory::DI_GENERATOR_CODE:
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
