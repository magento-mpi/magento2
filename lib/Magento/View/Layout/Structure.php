<?php
/**
 * An associative data structure, that features "nested set" parent-child relations
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout;

class Structure
{
    /**
     * Reserved keys for storing structural relations
     */
    const ATTRIBUTE_INTERNAL_ID = 'id';
    const ATTRIBUTE_ID = 'name';
    const ATTRIBUTE_ALIAS = 'as';

    const PARENT   = 'parent';
    const CHILDREN = 'children';
    const GROUPS = 'groups';

    /**
     * Increment number used for internal element IDs
     * @var int
     */
    private $inc = 0;

    /**
     * @var array
     */
    private $pairs = array();

    /**
     * @var array
     */
    private $elements = array();

    /**
     * @param $elementId
     * @param array $attributes
     * @return Structure
     */
    public function createElement($elementId, array $attributes)
    {
        $this->assertElementExists($elementId);

        foreach ($attributes as $key => $value) {
            $this->setAttribute($elementId, $key, $value);
        }

        return $this;
    }

    /**
     * Update an element information
     *
     * @param $elementId
     * @param array $attributes
     * @param bool $rewrite [optional]
     * @return Structure
     * @throws \Exception if an element doesn't exist
     */
    public function updateElement($elementId, array $attributes, $rewrite = false)
    {
        $this->assertElementExists($elementId);

        foreach ($attributes as $key => $value) {
            $this->setAttribute($elementId, $key, $value, $rewrite);
        }

        return $this;
    }

    /**
     * Get all elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Get existing element
     *
     * @param string $elementId
     * @return array|null
     */
    public function getElement($elementId)
    {
        return isset($this->pairs[$elementId]) ? $this->elements[$this->pairs[$elementId]] : null;
    }

    /**
     * Whether specified element exists
     *
     * @param string $elementId
     * @return bool
     */
    public function hasElement($elementId)
    {
        return isset($this->pairs[$elementId]);
    }

    /**
     * Remove element with specified ID from the structure
     *
     * Can recursively delete all child elements.
     * Returns false if there was no element found, therefore was nothing to delete.
     *
     * @param string $elementId
     * @param bool $recursive
     * @return bool
     */
    public function unsetElement($elementId, $recursive = true)
    {
        $wasFound = false;
        if (isset($this->pairs[$elementId])) {
            $this->unlinkFromParent($elementId);

            $wasFound = true;
            $internalId = $this->pairs[$elementId];
            if (isset($this->elements[$internalId][self::CHILDREN])) {
                foreach ($this->elements[$internalId][self::CHILDREN] as $childInternalId => $child) {
                    $childId = $child[self::ATTRIBUTE_ID];
                    if ($recursive) {
                        $this->unsetElement($childId, $recursive);
                    } else {
                        unset($this->elements[$childInternalId][self::PARENT]);
                    }
                }
            }
            unset($this->elements[$internalId]);
        }

        return $wasFound;
    }

    /**
     * Set an arbitrary value to specified element attribute
     *
     * @param string $elementId
     * @param string $attribute
     * @param mixed $value
     * @param bool $rewrite [optional]
     * @throws \InvalidArgumentException
     * @return Structure
     */
    public function setAttribute($elementId, $attribute, $value, $rewrite = false)
    {
        $internalId = $this->assertElementExists($elementId);

        switch ($attribute) {
            case self::CHILDREN:
            case self::PARENT:
            case self::GROUPS:
            case self::ATTRIBUTE_INTERNAL_ID:
                throw new \InvalidArgumentException("Attribute '{$attribute}' is reserved and cannot be set.");
            default:
                if (isset($this->elements[$internalId][$attribute])
                    && is_array($this->elements[$internalId][$attribute])
                    && is_array($value)) {
                    if (!$rewrite) {
                        $value = array_merge($this->elements[$internalId][$attribute], $value);
                    }
                }
                $this->elements[$internalId][$attribute] = $value;
        }

        return $this;
    }

    /**
     * Get element attribute
     *
     * @param string $elementId
     * @param string $attribute
     * @return mixed|null
     */
    public function getAttribute($elementId, $attribute)
    {
        $internalId = $this->assertElementExists($elementId);

        $result = null;
        if (isset($this->elements[$internalId][$attribute])) {
            $result = $this->elements[$internalId][$attribute];
        }

        return $result;
    }

    /**
     * Rename element ID
     *
     * @param string $oldId
     * @param string $newId
     * @return Structure
     * @throws \Exception if trying to overwrite another element
     */
    public function renameElement($oldId, $newId)
    {
        $internalId = $this->assertElementExists($oldId);

        if (!$newId) {
            throw new \Exception("Element ID should not be empty.");
        }
        if (isset($this->pairs[$newId])) {
            throw new \Exception("Element with ID '{$newId}' is already defined.");
        }

        $this->setAttribute($oldId, self::ATTRIBUTE_ID, $newId);

        unset($this->pairs[$oldId]);

        $this->pairs[$newId] = $internalId;

        return $this;
    }

    /**
     * Set element as a child to another element
     *
     * @param string $elementId
     * @param string $parentId
     * @param string $alias
     * @param int|null $position
     * @return Structure
     * @see insertChild() for position explanation
     * @throws \Exception if attempting to set parent as child to its child (recursively)
     */
    public function setAsChild($elementId, $parentId, $alias = '', $position = null)
    {
        if ($elementId == $parentId) {
            throw new \Exception("The '{$elementId}' cannot be set as child to itself.");
        }

        if ($this->isParentRecursively($elementId, $parentId)) {
            throw new \Exception("The '{$elementId}' is a parent of '{$parentId}' recursively, "
                . "therefore '{$elementId}' cannot be set as child to it."
            );
        }

        $this->unlinkFromParent($elementId);

        $this->insertChild($parentId, $elementId, $alias, $position);

        return $this;
    }

    /**
     * Unset element as a child of it's current parent
     *
     * @param $childId
     * @return Structure
     */
    public function unlinkFromParent($childId)
    {
        $childInternalId = $this->assertElementExists($childId);

        $parentId = $this->getParentId($childId);
        if ($parentId) {
            $parentInternalId = $this->assertElementExists($parentId);
            unset($this->elements[$parentInternalId][self::CHILDREN][$childInternalId]);
            unset($this->elements[$childInternalId][self::PARENT]);
        }

        return $this;
    }

    /**
     * Unset element as a child of another element
     *
     * Note that only parent-child relations will be deleted. Element itself will be retained.
     * @param string $parentId ID of parent element
     * @param string $alias
     * @return \Magento\Data\Structure
     */
    public function unsetChild($parentId, $alias)
    {
        $parentInternalId = $this->assertElementExists($parentId);

        if (isset($this->elements[$parentInternalId][self::CHILDREN])) {
            foreach ($this->elements[$parentInternalId][self::CHILDREN] as $childInternalId => $child) {
                if ($child[self::ATTRIBUTE_ALIAS] === $alias) {
                    unset($this->elements[$parentInternalId][self::CHILDREN][$childInternalId]);
                    unset($this->elements[$childInternalId][self::PARENT]);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Reorder a child element relatively to specified position
     *
     * Returns new position of the reordered element
     *
     * @param string $parentId
     * @param string $childId
     * @param int|null $position
     * @return int
     * @see insertChild() for position explanation
     */
    public function reorderChild($parentId, $childId, $position)
    {
        $parentInternalId = $this->assertElementExists($parentId);

        $alias = $this->getChildAlias($parentId, $childId);
        $currentOffset = $this->getChildOffset($parentId, $childId);
        $offset = $position;
        if ($position > 0) {
            if ($position >= $currentOffset + 1) {
                $offset -= 1;
            }
        } elseif ($position < 0) {
            if ($position < (($currentOffset + 1) - count($this->elements[$parentInternalId][self::CHILDREN]))) {
                if ($position === -1) {
                    $offset = null;
                } else {
                    $offset += 1;
                }
            }
        }

        $this->unlinkFromParent($childId);

        $this->insertChild($parentId, $childId, $alias, $offset);

        return $this->getChildOffset($parentId, $childId) + 1;
    }

    /**
     * Get element alias by name
     *
     * @param string $parentId
     * @param string $childId
     * @return string|null
     */
    public function getChildAlias($parentId, $childId)
    {
        $parentInternalId = $this->assertElementExists($parentId);
        $childInternalId = $this->assertElementExists($childId);

        $result = null;
        if (isset($this->elements[$parentInternalId][self::CHILDREN][$childInternalId])) {
            $result = $this->elements[$parentInternalId][self::CHILDREN][$childInternalId][self::ATTRIBUTE_ALIAS];
        }

        return $result;
    }

    /**
     * Reorder an element relatively to its sibling
     *
     * $offset possible values:
     *    1,  2 -- set after the sibling towards end -- by 1, by 2 positions, etc
     *   -1, -2 -- set before the sibling towards start -- by 1, by 2 positions, etc...
     *
     * Both $childId and $siblingId must be children of the specified $parentId
     * Returns new position of the reordered element
     *
     * @param string $parentId
     * @param string $childId
     * @param string $siblingId
     * @param int $offset
     * @return int
     */
    public function reorderToSibling($parentId, $childId, $siblingId, $offset)
    {
        $this->getChildOffset($parentId, $childId);

        if ($childId === $siblingId) {
            $newOffset = $this->getRelativeOffset($parentId, $siblingId, $offset);
            return $this->reorderChild($parentId, $childId, $newOffset);
        }
        $alias = $this->getChildAlias($parentId, $childId);

        $this->unlinkFromParent($childId);

        $newOffset = $this->getRelativeOffset($parentId, $siblingId, $offset);

        $this->insertChild($parentId, $childId, $alias, $newOffset);

        return $this->getChildOffset($parentId, $childId) + 1;
    }

    /**
     * Get child ID by parent ID and alias
     *
     * @param string $parentId
     * @param string $alias
     * @return string|null
     */
    public function getChildId($parentId, $alias)
    {
        $parentInternalId = $this->assertElementExists($parentId);
        $result = null;


        if (isset($this->elements[$parentInternalId][self::CHILDREN])) {
            foreach ($this->elements[$parentInternalId][self::CHILDREN] as $child) {
                if ($child[self::ATTRIBUTE_ALIAS] === $alias) {
                    $result = $child[self::ATTRIBUTE_ID];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get all children
     *
     * Returns in format 'id' => 'alias'
     *
     * @param string $parentId
     * @return array
     */
    public function getChildren($parentId)
    {
        $parentInternalId = $this->assertElementExists($parentId);

        $result = array();
        if (isset($this->elements[$parentInternalId][self::CHILDREN])) {
            foreach ($this->elements[$parentInternalId][self::CHILDREN] as $child) {
                $key = $child[self::ATTRIBUTE_ID];
                $value = $child[self::ATTRIBUTE_ALIAS];
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Get name of parent element
     *
     * @param string $childId
     * @return string|bool
     */
    public function getParentId($childId)
    {
        $result = null;
        if (isset($this->pairs[$childId])) {
            $childInternalId = $this->pairs[$childId];
            if (isset($this->elements[$childInternalId][self::PARENT])) {
                $result = $this->elements[$childInternalId][self::PARENT][self::ATTRIBUTE_ID];
            }
        }

        return $result;
    }

    /**
     * Add element to parent group
     *
     * @param string $childId
     * @param string $groupName
     * @return bool
     */
    public function addToParentGroup($childId, $groupName)
    {
        $parentId = $this->getParentId($childId);
        if ($parentId) {
            $parentInternalId = $this->assertElementExists($parentId);
            $childInternalId = $this->assertElementExists($childId);
            $this->elements[$parentInternalId][self::GROUPS][$groupName][$childInternalId] = $childInternalId;
            return true;
        }
        return false;
    }

    /**
     * Get element IDs for specified group
     *
     * Note that it is expected behavior if a child has been moved out from this parent,
     * but still remained in the group of old parent. The method will return only actual children.
     * This is intentional, in case if the child returns back to the old parent.
     *
     * @param string $parentId Name of an element containing group
     * @param string $groupName
     * @return array
     */
    public function getGroupChildNames($parentId, $groupName)
    {
        $parentInternalId = $this->assertElementExists($parentId);

        $result = array();
        if (isset($this->elements[$parentInternalId][self::GROUPS][$groupName])) {
            foreach ($this->elements[$parentInternalId][self::GROUPS][$groupName] as $childInternalId) {
                $result[] = $this->elements[$childInternalId][self::ATTRIBUTE_ID];
            }
        }

        return $result;
    }

    /**
     * Calculate new offset for placing an element relatively specified sibling under the same parent
     *
     * @param string $parentId
     * @param string $siblingId
     * @param int $delta
     * @return int
     */
    private function getRelativeOffset($parentId, $siblingId, $delta)
    {
        $newOffset = $this->getChildOffset($parentId, $siblingId) + $delta;
        if ($delta < 0) {
            $newOffset += 1;
        }
        if ($newOffset < 0) {
            $newOffset = 0;
        }

        return $newOffset;
    }

    /**
     * Calculate a relative offset of a child element in specified parent
     *
     * @param string $parentId
     * @param string $childId
     * @return int
     * @throws \Exception if specified elements have no parent-child relation
     */
    private function getChildOffset($parentId, $childId)
    {
        $index = array_search($childId, array_keys($this->getChildren($parentId)));
        if (false === $index) {
            throw new \Exception("The '{$childId}' is not a child of '{$parentId}'.");
        }

        return $index;
    }

    /**
     * Traverse through hierarchy and detect if the "potential parent" is a parent recursively to specified "child"
     *
     * @param string $childId
     * @param string $potentialParentId
     * @return bool
     */
    private function isParentRecursively($childId, $potentialParentId)
    {
        $parentId = $this->getParentId($potentialParentId);
        if (!$parentId) {
            $result = false;
        } elseif ($parentId === $childId) {
            $result = true;
        } else {
            $result = $this->isParentRecursively($childId, $parentId);
        }

        return $result;
    }

    /**
     * Insert an existing element as a child to existing element
     *
     * The element must not be a child to any other element
     * The target parent element must not have it as a child already
     *
     * Offset -- into which position to insert:
     *   0     -- set as 1st
     *   1,  2 -- after 1st, second, etc...
     *  -1, -2 -- before last, before second last, etc...
     *   null  -- set as last
     *
     * @param string $parentId
     * @param string $elementId
     * @param int|null $offset
     * @param string $alias
     * @throws \Exception
     */
    private function insertChild($parentId, $elementId, $alias, $offset)
    {
        $alias = $alias ?: $elementId;

        // validate
        $childInternalId = $this->assertElementExists($elementId);
        $parentInternalId = $this->assertElementExists($parentId);
        $childrenList = $this->getChildren($parentId);
        if (isset($childrenList[$elementId]) && ($childrenList[$elementId] != $alias) ) {
            throw new \Exception("The element '{$parentId}' already has a child with alias '{$alias}'");
        }

        // insert
        if (null === $offset) {
            $offset = count($childrenList);
        }

        if (isset($this->elements[$parentInternalId][self::CHILDREN])) {
            $children = $this->elements[$parentInternalId][self::CHILDREN];
        } else {
            $children = array();
        }

        $this->elements[$parentInternalId][self::CHILDREN] = array_merge(
            array_slice($children, 0, $offset),
            array(
                $childInternalId => array(
                    self::ATTRIBUTE_ALIAS => $alias,
                    self::ATTRIBUTE_ID => & $this->elements[$childInternalId][self::ATTRIBUTE_ID]
            )),
            array_slice($children, $offset)
        );
        $this->elements[$childInternalId][self::PARENT] = array(
            self::ATTRIBUTE_ID => & $this->elements[$parentInternalId][self::ATTRIBUTE_ID],
            self::ATTRIBUTE_INTERNAL_ID => $parentInternalId
        );
    }

    /**
     * Check if specified element exists
     *
     * @param string $elementId
     * @throws \Exception if doesn't exist
     * @return string
     */
    private function assertElementExists($elementId)
    {
        if (!isset($this->pairs[$elementId])) {
            $this->pairs[$elementId] = 'EL-' . $this->inc++;
            $this->elements[$this->pairs[$elementId]][self::ATTRIBUTE_INTERNAL_ID] = $this->pairs[$elementId];
            $this->elements[$this->pairs[$elementId]][self::ATTRIBUTE_ID] = $elementId;
            //throw new \Exception("No element found with ID '{$elementId}'.");
        }

        return $this->pairs[$elementId];
    }
}
