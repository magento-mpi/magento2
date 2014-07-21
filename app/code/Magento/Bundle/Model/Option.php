<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model;

/**
 * Bundle Option Model
 *
 * @method int getParentId()
 * @method int getPosition()
 * @method int getRequired()
 * @method null|\Magento\Catalog\Model\Product[] getSelections()
 * @method string getType()
 * @method Option setParentId(int $value)
 * @method Option setPosition(int $value)
 * @method Option setRequired(int $value)
 * @method Option setType(string $value)
 */
class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Default selection object
     *
     * @var \Magento\Catalog\Model\Product|null
     */
    protected $defaultSelection = null;

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
     * @param \Magento\Catalog\Model\Product $selection
     * @return void
     */
    public function addSelection(\Magento\Catalog\Model\Product $selection)
    {
        $selections = $this->getDataSetDefault('selections', []);
        $selections[] = $selection;
        $this->setSelections($selections);
    }

    /**
     * Check Is Saleable Option
     *
     * @return bool
     */
    public function isSaleable()
    {
        $saleable = false;
        $selections = $this->getSelections();
        if ($selections) {
            foreach ($selections as $selection) {
                if ($selection->isSaleable()) {
                    $saleable = true;
                    break;
                }
            }
        }
        return $saleable;
    }

    /**
     * Retrieve default Selection object
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getDefaultSelection()
    {
        if (!$this->defaultSelection && $this->getSelections()) {
            foreach ($this->getSelections() as $selection) {
                if ($selection->getIsDefault()) {
                    $this->defaultSelection = $selection;
                    break;
                }
            }
        }
        return $this->defaultSelection;
    }

    /**
     * Check is multi Option selection
     *
     * @return bool
     */
    public function isMultiSelection()
    {
        return $this->getType() == 'checkbox' || $this->getType() == 'multi';
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
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getSelectionById($selectionId)
    {
        $foundSelection = null;
        foreach ($this->getSelections() as $selection) {
            if ($selection->getSelectionId() == $selectionId) {
                $foundSelection = $selection;
                break;
            }
        }
        return $foundSelection;
    }
}
