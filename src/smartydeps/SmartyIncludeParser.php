<?php
namespace Smartydeps;

use Smartydeps\ast\ASTKind;
use Smartydeps\ast\ASTWalker;

class SmartyIncludeParser extends ASTWalker
{
    /** @var string[] $includes */
    private $includes = [];

    /** @var IncludePathResolver */
    private $path_resolver = null;

    /** @var VariableNameMap */
    private $variable_name_map = null;

    public function __construct(IncludePathResolver $resolver)
    {
        parent::__construct();
        $this->path_resolver = $resolver;
        $this->variable_name_map = new VariableNameMap();
    }

    protected function enter($node)
    {
        if ($node->kind === ASTKind::AST_ASSIGN) {
            $assign_from = $this->toVarName($node->children[0]);
            $assign_to = $this->toVarName($node->children[1]);
            $resolved_assign_to = $this->variable_name_map->resolve($assign_to);
            if ($assign_from === '$this->_tpl_vars' || $assign_from === '$_smarty_tpl_vars') { //FIXME
                return;
            }
            $this->variable_name_map->addAlias($assign_from, $resolved_assign_to);
        } else if ($node->kind === ASTKind::AST_FOREACH) {
            $assign_from = $this->toVarName($node->children[1]);
            $assign_to = $this->toVarName($node->children[0]);
            $resolved_assign_to = $this->variable_name_map->resolve($assign_to);
            $this->variable_name_map->addAlias($assign_from, $resolved_assign_to);
        }
    }

    protected function leave($node)
    {
        if ($node->kind === ASTKind::AST_UNSET) {
            $unset_target = $this->toVarName($node->children[0]);
            $this->variable_name_map->deleteAlias($unset_target);
        } else if ($node->kind === ASTKind::AST_FOREACH) {
            $unset_target = $this->toVarName($node->children[0]);
            $this->variable_name_map->deleteAlias($unset_target);
        }
    }

    /**
     * @param string $parsed_template
     * @return string[]
     */
    public function parse($parsed_template)
    {
        $node = \ast\parse_code($parsed_template);
        $this->walk($node);
        $result = array_unique($this->includes);
        sort($result);
        return $result;
    }

    protected function visit($node)
    {
        if (
            $node->kind === ASTKind::AST_METHOD_CALL
            && $node->children[0]->kind === ASTKind::AST_VAR && $node->children[0]->children[0] === "this"
            && $node->children[1] === "_smarty_include"
        ) {
            $name = $this->toVarName($node->children[2]->children[0]->children[0]);
            $real_variable_name = $this->variable_name_map->resolve($name);
            $this->includes = array_merge($this->includes, $this->path_resolver->resolve($real_variable_name));
        }
    }
}
