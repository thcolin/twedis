<?php
	
	namespace App\Core;

/**
 * Class Top
 *
 * @package Lib\Sort
 */
class Sort
{
    /**
     * Unsorted nodes
     *
     * @var array
     */
    protected $_nodes = array();

    /**
     * Nodes structure
     *
     * @var array
     */
    protected $_structure = array();

    /**
     * Stored nodes
     *
     * @var array|null
     */
    protected $_sortedNodes;

    /**
     * Stored nodes
     *
     * @var array
     */
    protected $_level = 0;

    /**
     * Status of mode "single non-edge node"
     *
     * @var bool
     * @see setModeSingleNonEdgeNode()
     */
    protected $_singleNonEdgeNode = true;

    /**
     * Get status of "Single non-edge node" mode
     *
     * @return boolean
     * @see setModeSingleNonEdgeNode()
     */
    public function isModeSingleNonEdgeNode()
    {
        return $this->_singleNonEdgeNode;
    }

    /**
     * Set status of "Single non-edge node" mode
     *
     * This status means that sorting will move only first non-edged node to top.
     * Rest non-edge nodes will be added according to sorting in _nodes property
     * In case it will FALSE all nodes will be moved to top.
     *
     * @param boolean $flag
     * @return $this
     */
    public function enableModeSingleNonEdgeNode($flag)
    {
        $this->_singleNonEdgeNode = (bool)$flag;
        return $this;
    }

    /**
     * Add node
     *
     * @param string $name
     * @param array  $dependsOn
     * @throws Exception
     * @return $this
     */
    public function addNode($name, array $dependsOn = array())
    {
        if (null !== $this->_sortedNodes) {
            throw new Exception('Nodes already sorted.');
        }
        $this->_nodes[$name]     = $name;
        $this->_structure[$name] = $dependsOn;
        return $this;
    }

    /**
     * Sort
     *
     * @return array
     */
    public function getSortedNodes()
    {
        if (null === $this->_sortedNodes) {
            $this->_sortedNodes = array();
            //insert non-edged nodes
            $this->_performNonEdgedNodes();
            //insert edged nodes
            $this->_performEdgedNodes();
        }
        return $this->_sortedNodes;
    }

    /**
     * Move node into sorted list
     *
     * @param string $name
     * @throws Exception
     * @return $this
     */
    protected function _moveNodeToSortedList($name)
    {
        $node = $this->_takeNode($name);
        if ($node) {
            $this->_sortedNodes[] = $node;
        } else {
            throw new Exception("The node '$name' has already been taken.");
        }
        return $this;
    }

    /**
     * Take node from the list
     *
     * @param string $name
     * @return string|null
     */
    protected function _takeNode($name)
    {
        if (!isset($this->_nodes[$name])) {
            return null;
        }
        $node = $this->_nodes[$name];
        unset($this->_nodes[$name]);
        return $node;
    }

    /**
     * Perform node sorting
     *
     * @param string $name
     * @return $this
     * @throws Exception
     */
    protected function _performNode($name)
    {
        $node = $this->_takeNode($name);
        if (null === $node) {
            return $this;
        }
        foreach ($this->_structure[$node] as $depNode) {
            $this->_checkCycledEdges($node, $depNode);
            $this->_performNode($depNode);
        }
        $this->_sortedNodes[] = $node;
        return $this;
    }

    /**
     * Check cycled edges
     *
     * @param string $node
     * @param string $edgeNode
     * @return bool
     * @throws Exception
     */
    protected function _checkCycledEdges($node, $edgeNode)
    {
        if (in_array($node, $this->_structure[$edgeNode])) {
            throw new Exception("Cyclic dependencies between $edgeNode and $node.");
        }
        return true;
    }

    /**
     * Perform edged nodes
     *
     * @return $this
     */
    protected function _performEdgedNodes()
    {
        while (!empty($this->_nodes)) {
            $name = current($this->_nodes);
            $this->_performNode($name);
        }
        return $this;
    }

    /**
     * Perform non-edged nodes
     *
     * @return $this
     */
    protected function _performNonEdgedNodes()
    {
        foreach ($this->_structure as $name => $edges) {
            if (!$edges) {
                $this->_moveNodeToSortedList($name);
                if ($this->isModeSingleNonEdgeNode()) {
                    //to add only first node and to add rest non-edge nodes according to sorting in _nodes property
                    break;
                }
            }
        }
        return $this;
    }
}
	
?>