<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Director_Dom extends Mage_Backend_Model_Menu_Builder_DirectorAbstract
{
    /**
     * Extracted config data
     * @var array
     */
    protected $_extractedData = array();

    /**
     * @param array $data
     * @throws InvalidArgumentException if config storage is not present in $data array
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        if (false == ($this->_configModel instanceof DOMDocument)) {
            throw new InvalidArgumentException('Configuration storage model is not instance of DOMDocument');
        }
        $this->_extractData();
    }

    /**
     * Extract data from DOMDocument
     * @return Mage_Backend_Model_Menu_Builder_Director_Dom
     */
    protected function _extractData()
    {
        $attributeNamesList = array(
            'id',
            'title',
            'module',
            'sortOrder',
            'action',
            'parent',
            'resource',
            'dependsOnModule',
            'dependsOnConfig',
        );
        $xpath = new DOMXPath($this->_configModel);
        $nodeList = $xpath->query('/config/menu/*');
        for ($i = 0; $i < $nodeList->length; $i++) {
            $item = array();
            $node = $nodeList->item($i);
            $item['type'] = $node->nodeName;
            foreach ($attributeNamesList as $name) {
                $item[$name] = $node->hasAttribute($name) ? $node->getAttribute($name) : null;
            }
            $this->_extractedData[] = $item;
        }
    }

    /**
     * Get data that were extracted from config storage
     * @return array
     */
    public function getExtractedData()
    {
        return $this->_extractedData;
    }

    /**
     * Get command object
     * @param array $data command params
     * @return Mage_Backend_Model_Menu_Builder_CommandAbstract
     */
    protected function _getCommand($data)
    {
        switch ($data['type']) {
            case 'update' : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Update',
                    array($data)
                );
            } break;

            case 'remove' : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Remove',
                    array($data)
                );
            } break;

            default : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Add',
                    array($data)
                );
            } break;
        }
        return $command;
    }

    /**
     *
     * @param Mage_Backend_Model_Menu_BuilderAbstract $builder
     * @throws InvalidArgumentException if invalid builder object
     * @return Mage_Backend_Model_Menu_Builder_DirectorAbstract
     */
    public function buildMenu($builder)
    {
        if (false == ($builder instanceof Mage_Backend_Model_Menu_BuilderAbstract)) {
            throw new InvalidArgumentException('Builder is not instance of Mage_Backend_Model_Menu_BuilderAbstract');
        }
        foreach ($this->getExtractedData() as $data) {
            $command = $this->_getCommand($data);
            $builder->processCommand($command);
        }
        return $this;
    }
}
