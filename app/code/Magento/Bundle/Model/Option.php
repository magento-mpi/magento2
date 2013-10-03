<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Option Model
 *
 * @method \Magento\Bundle\Model\Resource\Option _getResource()
 * @method \Magento\Bundle\Model\Resource\Option getResource()
 * @method int getParentId()
 * @method \Magento\Bundle\Model\Option setParentId(int $value)
 * @method int getRequired()
 * @method \Magento\Bundle\Model\Option setRequired(int $value)
 * @method int getPosition()
 * @method \Magento\Bundle\Model\Option setPosition(int $value)
 * @method string getType()
 * @method \Magento\Bundle\Model\Option setType(string $value)
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Model;

class Option extends \Magento\Core\Model\AbstractModel
{
    /**
     * Default selection object
     *
     * @var \Magento\Bundle\Model\Selection
     */
    protected $_defaultSelection = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Resource\Option');
        parent::_construct();
    }

    /**
     * Add selection to option
     *
     * @param \Magento\Bundle\Model\Selection $selection
     * @return \Magento\Bundle\Model\Option
     */
    public function addSelection($selection)
    {
        if (!$selection) {
            return false;
        }
        if (!$selections = $this->getData('selections')) {
            $selections = array();
        }
        array_push($selections, $selection);
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
            return (bool)$saleable;
        } else {
            return false;
        }
    }

    /**
     * Retrieve default Selection object
     *
     * @return \Magento\Bundle\Model\Selection
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
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }

    /**
     * Return selection by it's id
     *
     * @param int $selectionId
     * @return \Magento\Bundle\Model\Selection
     */
    public function getSelectionById($selectionId)
    {
        $selections = $this->getSelections();
        $i = count($selections);

        while ($i-- && $selections[$i]->getSelectionId() != $selectionId);

        return $i == -1 ? false : $selections[$i];
    }
}
