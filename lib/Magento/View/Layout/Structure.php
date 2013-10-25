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

/**
 * Structure
 *
 * @package Magento\View
 */
class Structure
{
    /**#@+
     * Reserved keys for storing structural relations
     */
    const ATTRIBUTE_ID = 'name';
    const ATTRIBUTE_ALIAS = 'as';

    const PARENT   = 'parent';
    const CHILDREN = 'children';
    const GROUPS = 'groups';
    /**#@-*/

    /**
     * @var array
     */
    private $elements = array();

    /**
     * Set structure elements from external source
     *
     * @param array $elements
     * @throws \Magento\Exception if any format issues identified
     */
    public function importElements(array $elements)
    {
        $this->elements = $elements;

        foreach ($this->elements as $elementId => $element) {
            if (is_numeric($elementId)) {
                throw new \Magento\Exception("Element ID must not be numeric: '{$elementId}'.");
            }
            $this->assertParentRelation($elementId);
            if (isset($element[self::GROUPS])) {
                $groups = $element[self::GROUPS];
                $this->assertArray($groups);
                foreach ($groups as $groupName => $group) {
                    $this->assertArray($group);
                    if ($group !== array_flip($group)) {
                        throw new \Magento\Exception(
                            "Invalid format of group '{$groupName}': " . var_export($group, 1)
                        );
                    }
                    foreach ($group as $groupElementId) {
                        $this->assertElementExists($groupElementId);
                    }
                }
            }
        }
    }

    public function exportElements()
    {
        return $this->elements;
    }
    /**
     * Create new element
     *
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
        return isset($this->elements[$elementId]) ? $this->elements[$elementId] : null;
    }

    /**
     * Whether specified element exists
     *
     * @param string $elementId
     * @return bool
     */
    public function hasElement($elementId)
    {
        return isset($this->elements[$elementId]);
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
        if (isset($this->elements[$elementId][self::CHILDREN])) {
            foreach (array_keys($this->elements[$elementId][self::CHILDREN]) as $childId) {
                $this->assertElementExists($childId);
                if ($recursive) {
                    $this->unsetElement($childId, $recursive);
                } else {
                    unset($this->elements[$childId][self::PARENT]);
                }
            }
        }
        $wasFound = isset($this->elements[$elementId]);
        unset($this->elements[$elementId]);
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
        $this->assertElementExists($elementId);

        switch ($attribute) {
            case self::CHILDREN:
            case self::PARENT:
            case self::GROUPS:
                throw new \InvalidArgumentException("Attribute '{$attribute}' is reserved and cannot be set.");
            default:
                if (isset($this->elements[$elementId][$attribute])
                    && is_array($this->elements[$elementId][$attribute])
                    && is_array($value)) {
                    if (!$rewrite) {
                        $value = array_merge($this->elements[$elementId][$attribute], $value);
                    }
                }
                $this->elements[$elementId][$attribute] = $value;
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
        $this->assertElementExists($elementId);

        return isset($this->elements[$elementId][$attribute]) ? $this->elements[$elementId][$attribute] : null;
    }

    /**
     * Rename element ID
     *
     * @param string $oldId
     * @param string $newId
     * @return Structure
     * @throws \Magento\Exception if trying to overwrite another element
     */
    public function renameElement($oldId, $newId)
    {
        $this->assertElementExists($oldId);
        if (!$newId || isset($this->elements[$newId])) {
            throw new \Magento\Exception("Element with ID '{$newId}' is already defined.");
        }

        // rename in registry
        $this->elements[$newId] = $this->elements[$oldId];

        // rename references in children
        if (isset($this->elements[$oldId][self::CHILDREN])) {
            foreach (array_keys($this->elements[$oldId][self::CHILDREN]) as $childId) {
                $this->assertElementExists($childId);
                $this->elements[$childId][self::PARENT] = $newId;
            }
        }

        // rename key in its parent's children array
        if (isset($this->elements[$oldId][self::PARENT]) && $parentId = $this->elements[$oldId][self::PARENT]) {
            $alias = $this->elements[$parentId][self::CHILDREN][$oldId];
            $offset = $this->getChildOffset($parentId, $oldId);
            unset($this->elements[$parentId][self::CHILDREN][$oldId]);
            $this->setAsChild($newId, $parentId, $alias, $offset);
        }

        unset($this->elements[$oldId]);
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
            throw new \Exception(
                "The '{$elementId}' is a parent of '{$parentId}' recursively, "
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
        $parentId = $this->getParentId($childId);
        if ($parentId) {
            unset($this->elements[$parentId][self::CHILDREN][$childId]);
            if (empty($this->elements[$parentId][self::CHILDREN])) {
                unset($this->elements[$parentId][self::CHILDREN]);
            }
            unset($this->elements[$childId][self::PARENT]);
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
        $childId = $this->getChildId($parentId, $alias);

        $this->unlinkFromParent($childId);
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
        $alias = $this->getChildAlias($parentId, $childId);
        $currentOffset = $this->getChildOffset($parentId, $childId);
        $offset = $position;
        if ($position > 0) {
            if ($position >= $currentOffset + 1) {
                $offset -= 1;
            }
        } elseif ($position < 0) {
            if ($position < (($currentOffset + 1) - count($this->elements[$parentId][self::CHILDREN]))) {
                if ($position === -1) {
                    $offset = null;
                } else {
                    $offset += 1;
                }
            }
        }
        $this->unlinkFromParent($childId);
        $this->insertChild($parentId, $childId, $offset, $alias);
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
        if (isset($this->elements[$parentId][self::CHILDREN][$childId])) {
            return $this->elements[$parentId][self::CHILDREN][$childId];
        }
        return false;
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
        if (isset($this->elements[$parentId][self::CHILDREN])) {
            return array_search($alias, $this->elements[$parentId][self::CHILDREN]);
        }
        return false;
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
        return isset($this->elements[$parentId][self::CHILDREN])
            ? $this->elements[$parentId][self::CHILDREN] : array();
    }

    /**
     * Get name of parent element
     *
     * @param string $childId
     * @return string|bool
     */
    public function getParentId($childId)
    {
        return isset($this->elements[$childId][self::PARENT])
            ? $this->elements[$childId][self::PARENT] : false;
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
            $this->assertElementExists($parentId);
            $this->elements[$parentId][self::GROUPS][$groupName][$childId] = $childId;
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
        $result = array();
        if (isset($this->elements[$parentId][self::GROUPS][$groupName])) {
            foreach ($this->elements[$parentId][self::GROUPS][$groupName] as $childId) {
                if (isset($this->elements[$parentId][self::CHILDREN][$childId])) {
                    $result[] = $childId;
                }
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
     * @throws \Magento\Exception if specified elements have no parent-child relation
     */
    private function getChildOffset($parentId, $childId)
    {
        $index = array_search($childId, array_keys($this->getChildren($parentId)));
        if (false === $index) {
            throw new \Magento\Exception("The '{$childId}' is not a child of '{$parentId}'.");
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
     * @param string $targetParentId
     * @param string $elementId
     * @param int|null $offset
     * @param string $alias
     * @throws \Magento\Exception
     */
    private function insertChild($targetParentId, $elementId, $alias, $offset)
    {
        $alias = $alias ?: $elementId;

        // validate
        $this->assertElementExists($elementId);
        if (!empty($this->elements[$elementId][self::PARENT])) {
            throw new \Magento\Exception(
                "The element '{$elementId}' already has a parent: '{$this->elements[$elementId][self::PARENT]}'"
            );
        }
        $this->assertElementExists($targetParentId);
        $children = $this->getChildren($targetParentId);
        if (isset($children[$elementId])) {
            throw new \Magento\Exception("The element '{$elementId}' already a child of '{$targetParentId}'");
        }
        if (false !== array_search($alias, $children)) {
            throw new \Magento\Exception("The element '{$targetParentId}' already has a child with alias '{$alias}'");
        }

        // insert
        if (null === $offset) {
            $offset = count($children);
        }
        $this->elements[$targetParentId][self::CHILDREN] = array_merge(
            array_slice($children, 0, $offset),
            array($elementId => $alias),
            array_slice($children, $offset)
        );
        $this->elements[$elementId][self::PARENT] = $targetParentId;
    }

    /**
     * Check if specified element exists
     *
     * @param string $elementId
     * @throws \Magento\Exception if doesn't exist
     */
    private function assertElementExists($elementId)
    {
        if (!isset($this->elements[$elementId])) {
            $this->elements[$elementId] = array();
            //throw new \Magento\Exception("No element found with ID '{$elementId}'.");
        }
    }

    /**
     * Verify relations of parent-child
     *
     * @param string $elementId
     * @throws \Magento\Exception
     */
    private function assertParentRelation($elementId)
    {
        $element = $this->elements[$elementId];

        // element presence in its parent's nested set
        if (isset($element[self::PARENT])) {
            $parentId = $element[self::PARENT];
            $this->assertElementExists($parentId);
            if (!$this->getChildAlias($parentId, $elementId)) {
                throw new \Magento\Exception(
                    "Broken parent-child relation: the '{$elementId}' is not in the nested set of '{$parentId}'."
                );
            }
        }

        // element presence in its children
        if (isset($element[self::CHILDREN])) {
            $children = $element[self::CHILDREN];
            $this->assertArray($children);
            if ($children !== array_flip(array_flip($children))) {
                throw new \Magento\Exception('Invalid format of children: ' . var_export($children, 1));
            }
            foreach (array_keys($children) as $childId) {
                $this->assertElementExists($childId);
                if (!isset($this->elements[$childId][self::PARENT])
                    || $elementId !== $this->elements[$childId][self::PARENT]
                ) {
                    throw new \Magento\Exception(
                        "Broken parent-child relation: the '{$childId}' is supposed to have '{$elementId}' as parent."
                    );
                }
            }
        }
    }

    /**
     * Check if it is an array
     *
     * @param array $value
     * @throws \Magento\Exception
     */
    private function assertArray($value)
    {
        if (!is_array($value)) {
            throw new \Magento\Exception("An array expected: " . var_export($value, 1));
        }
    }
}
