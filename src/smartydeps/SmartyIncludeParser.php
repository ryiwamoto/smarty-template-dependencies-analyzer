<?php
namespace smartydeps;

class SmartyIncludeParser extends ASTWalker
{
    /** @var string[] $includes */
    private $includes = [];

    /** @var IncludePathResolver */
    private $path_resolver = null;

    public function __construct(IncludePathResolver $resolver)
    {
        $this->path_resolver = $resolver;
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
            $node->kind === 768 /* method call*/
            && $node->children[0]->kind === 256 && $node->children[0]->children[0] === "this"
            && $node->children[1] === "_smarty_include"
        ) {
            $template_name = $this->getTemplateName($node->children[2]->children[0]->children[0]);
            $this->includes = array_merge($this->includes, $this->path_resolver->resolve($template_name));
        }
    }

    private function getTemplateName($node)
    {
        if (is_string($node)) {
            return $node;
        } else if ($node->kind === 525 /* AST_ARRAY_ELEM */) {
            return $this->getTemplateName($node->children[0]);
        } else if ($node->kind === 512 /* AST_DIM */) {
            $prev = $node->children[0]->kind === 513 ? "$" : $this->getTemplateName($node->children[0]) . ".";
            return $prev . $node->children[1];
        } else {
            return "";
        }
    }
}
