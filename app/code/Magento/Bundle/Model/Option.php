<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model;

/**
 * Bundle Option Model
 *
 * @method int getParentId()
 * @method \Magento\Bundle\Model\Option setParentId(int $value)
 * @method int getRequired()
 * @method \Magento\Bundle\Model\Option setRequired(int $value)
 * @method int getPosition()
 * @method \Magento\Bundle\Model\Option setPosition(int $value)
 * @method string getType()
 * @method \Magento\Bundle\Model\Option setType(string $value)
 * @method \Magento\Catalog\Model\Product[] getSelections()
 */
class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Default selection object
     *
     * @var Selection
     */
    protected $_defaultSelection = null;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Resource\Option');
        parent::_construct();
    }

    /**
     * Add selection to option
     *
     * @param Selection $selection
     * @return $this|false
     */
    public function addSelection($selection)
    {
        if (!$selection) {
            return false;
        }
        $selections = $this->getDataSetDefault('selections', array());
        $selections[] = $selection;
        $this->setSelections($selections);
        return $this;
    }

    /**
     * Check Is Saleable Option
     *
     * @return bool
     */
    public function isSaleable()
    {
        $saleable = 0;
        if ($this->getSelections()) {
            foreach ($this->getSelections() as $selection) {
                if ($selection->isSaleable()) {
                    $saleable++;
                }
            }
            return (bool) $saleable;
        } else {
            return false;
        }
    }

    /**
     * Retrieve default Selection object
     *
     * @return Selection
     */
    public function getDefaultSelection()
    {
        if (!$this->_defaultSelection && $this->getSelections()) {
            foreach ($this->getSelections() as $selection) {
                if ($selection->getIsDefault()) {
                    $this->_defaultSelection = $selection;
                    break;
                }
            }
        }
        return $this->_defaultSelection;
    }

    /**
     * Check is multi Option selection
     *
     * @return bool
     */
    public function isMultiSelection()
    {
        if ($this->getType() == 'checkbox' || $this->getType() == 'multi') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve options searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()->getSearchableData($productId, $storeId);
    }

    /**
     * Return selection by it's id
     *
     * @param int $selectionId
     * @return Selection|false
     */
    public function getSelectionById($selectionId)
    {
        foreach ($this->getSelections() as $option) {
            if ($option->getSelectionId() == $selectionId) {
                return $option;
            }
        }
        return false;
    }
}
