<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader\Soap;

/**
 * SOAP API specific class reflector.
 */
class ClassReflector extends \Magento\Webapi\Model\Soap\Config\Reader\ClassReflectorAbstract
{
    /**
     * Set types data into reader after reflecting all files.
     *
     * @return array
     */
    public function getPostReflectionData()
    {
        return array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
        );
    }
}
