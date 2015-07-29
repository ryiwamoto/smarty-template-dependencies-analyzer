<?php

namespace Smartydeps;

class DependencyNodes
{
    private $nodes = [];

    /**
     * @param $key
     * @return string[]
     */
    public function get($key)
    {
        return $this->nodes[$key] ?? [];
    }

    /**
     * @param string $key
     * @param string|string[] $node
     */
    public function add($key, $node)
    {
        if (!is_array($node)) {
            $node = [$node];
        }
        if (!isset($this->nodes[$key])) {
            $this->nodes[$key] = [];
        }
        $this->nodes[$key] = array_unique(array_merge($this->nodes[$key], $node));
    }

    public function files()
    {
        return array_keys($this->nodes);
    }
}