<?php
namespace Smartydeps;

class VariableNameMap
{
    private $map = [];

    public function addAlias($alias_name, $real_name)
    {
        $alias = $this->resolve($alias_name);
        $real = $this->resolve($real_name);
        if($alias === ''){
            return;
        }
        $this->map[$alias] = $real;
    }

    public function deleteAlias($alias_name)
    {
        unset($this->map[$alias_name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function resolve($name)
    {
        $aliases = array_keys($this->map);
        foreach ($aliases as $alias) {
            if (strpos($name, $alias) === 0) {
                $rest = str_replace($alias, '', $name);
                return $this->map[$alias] . $rest;
            }
        }
        return $name;
    }
}
