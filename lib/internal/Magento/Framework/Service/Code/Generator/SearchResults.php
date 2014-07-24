<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Code\Generator\EntityAbstract;

/**
 * Class Builder
 */

class SearchResults extends EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'searchResults';

    /**
     * Search result default class
     */
    const SEARCH_RESULT = '\\Magento\Framework\Service\V1\Data\SearchResults';

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        return [];
    }

    /**
     * Returns list of methods for class generator
     *
     * @return array
     */
    protected function _getClassMethods()
    {
        return [];
    }

    protected function _getDefaultConstructorDefinition()
    {

    }


    /**
     * Generate code
     *
     * @return string
     */
    protected function _generateCode()
    {
        $this->_classGenerator->setName($this->_getResultClassName())
            ->setExtendedClass(self::SEARCH_RESULT);
        return $this->_getGeneratedCode();
    }
}
