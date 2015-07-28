<?php

namespace Smartydeps;

abstract class ASTWalker
{
    /**
     * @param ast\Node|string $node
     */
    protected function walk($node)
    {
        $this->visit($node);
        if ($node instanceof \ast\Node) {
            foreach ($node->children as $child) {
                $this->walk($child);
            }
        }
    }

    protected function visit($node)
    {
    }
}
