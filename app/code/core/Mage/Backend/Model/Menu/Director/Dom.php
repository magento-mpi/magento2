<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Director_Dom extends Mage_Backend_Model_Menu_DirectorAbstract
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
     * @return Mage_Backend_Model_Menu_Director_Dom
     */
    protected function _extractData()
    {
        $attributeNamesList = array(
            'id',
            'title',
            'toolTip',
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
            case 'item' : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Create',
                    $data
                );
                } break;

            case 'update' : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Update',
                    $data
                );
            } break;

            case 'remove' : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Remove',
                    $data
                );
            } break;

            default : {
                $command = $this->_factory->getModelInstance(
                    'Mage_Backend_Model_Menu_Builder_Command_Add',
                    $data
                );
            } break;
        }
        return $command;
    }

    /**
     *
     * @param Mage_Backend_Model_Menu_Builder $builder
     * @throws InvalidArgumentException if invalid builder object
     * @return Mage_Backend_Model_Menu_DirectorAbstract
     */
    public function buildMenu(Mage_Backend_Model_Menu_Builder $builder)
    {
        foreach ($this->getExtractedData() as $data) {
            $command = $this->_getCommand($data);
            $builder->processCommand($command);
        }
        return $this;
    }
}
