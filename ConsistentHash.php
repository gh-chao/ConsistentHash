<?php

/**
 * ConsitentHash
 * Date: 14/12/22
 * @author leo <897798676@qq.com>
 */
class ConsitentHash
{

    private $virtualNode;

    /**
     * @var callable
     */
    private $hashing;

    /**
     * nodes
     * @var array
     */
    private $nodes;


    /**
     * virtualNodes
     * @var array
     */
    private $virtualNodes;


    /**
     * is sort
     * @var boolean
     */
    private $isSort = false;


    /**
     * construct
     * @param integer $virtualNode
     * @param callable|string $hashing
     */
    public function __construct($virtualNode = 64, $hashing = 'crc32')
    {
        $this->virtualNode = $virtualNode;
        $this->hashing = $hashing;
    }

    /**
     * add node
     * @param string $node
     * @return $this
     */
    public function addNode($node)
    {
        if (isset($this->nodes[$node])) {
            return $this;
        }
        $this->rltVirtualNodes[$node] = array();
        for ($i = 0; $i < $this->virtualNode; $i++) {
            $hash = call_user_func($this->hashing, $node . $i);
            $this->virtualNodes[$hash] = $node;
            $this->rltVirtualNodes[$node][] = $hash;
        }
        $this->isSort = false;
        return $this;
    }

    /**
     * add nodes
     * @param array $nodes
     * @return $this
     */
    public function addNodes(array $nodes)
    {
        foreach ($nodes as $node) {
            $this->addNode($node);
        }
        return $this;
    }

    /**
     * get node
     * @param  string $key
     * @return string
     * @throws Exception
     */
    public function getNode($key)
    {
        if (empty($this->virtualNodes)) {
            throw new Exception("No nodes exist");
        }

        if (!$this->isSort) {
            ksort($this->virtualNodes);
            $this->isSort = true;
        }

        $hash = call_user_func($this->hashing, $key);

        foreach ($this->virtualNodes as $vritualNode => $node) {
            if ($vritualNode > $hash) {
                return $node;
            }
        }

        reset($this->virtualNodes);
        return current($this->virtualNodes);
    }


}