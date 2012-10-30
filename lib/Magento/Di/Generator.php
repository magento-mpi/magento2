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
     * @param Magento_Autoload $autoloader
     */
    public function __construct(Magento_Autoload $autoloader = null)
    {
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
        switch ($entity) {
            case Magento_Di_Generator_Factory::DI_GENERATOR_CODE:
            default:
                $generator = new Magento_Di_Generator_Factory($entityName, $className);
                break;
        }
        if (!$generator->generate()) {
            $errors = $generator->getErrors();
            throw new Magento_Exception(implode(' ', $errors));
        }

        return true;
    }
}
