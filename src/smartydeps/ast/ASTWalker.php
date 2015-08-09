<?php

namespace Smartydeps\ast;

abstract class ASTWalker
{
    public function __construct()
    {
    }

    protected function enter($node)
    {
    }

    protected function leave($node)
    {

    }

    /**
     * @param ast\Node|string $node
     */
    protected function walk($node)
    {
        $this->visit($node);
        if ($node instanceof \ast\Node) {
            foreach ($node->children as $child) {
                $this->enter($child);
                $this->walk($child);
                $this->leave($child);
            }
        }
    }

    protected function visit($node)
    {
    }

    /**
     * @param $node
     * @return string
     */
    public function toVarName($node)
    {
        if (is_string($node)) {
            return $node;
        } else if ($node->kind === ASTKind::AST_ARRAY_ELEM) {
            return $this->toVarName($node->children[0]);
        } else if ($node->kind === ASTKind::AST_DIM) {
            $prev = $node->children[0]->kind === ASTKind::AST_PROP ? "$" : $this->toVarName($node->children[0]) . '.';
            return $prev . $this->toVarName($node->children[1]);
        } else if ($node->kind === ASTKind::AST_PROP) {
            return $this->toVarName($node->children[0]) . '->' . $this->toVarName($node->children[1]);
        } else if ($node->kind === ASTKind::AST_VAR) {
            return "$" . $this->toVarName($node->children[0]);
        } else {
            return '';
        }
    }
}
