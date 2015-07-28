<?php
namespace Smartydeps;

class IncludePathResolver
{
    /** @var string */
    private $template_dir;

    /** @var array */
    private $template_variables;

    /**
     * @param string $template_path
     * @param array $template_variables
     */
    public function __construct($template_path, $template_variables)
    {
        $this->template_dir = $template_path;
        $this->template_variables = $template_variables;
    }

    /**
     * @param string $template_path
     * @return string[]
     */
    public function resolve($template_path)
    {
        if ($this->isVariable($template_path)) {
            return $this->resolveVariable($template_path);
        } else {
            return [$template_path];
        }
    }

    private function isVariable($template_name)
    {
        return strpos($template_name, "$") === 0;
    }

    private function resolveVariable($variable)
    {
        if (!isset($this->template_variables[$variable])) {
            throw new \InvalidArgumentException("$variable is not defined.");
        }
        return $this->template_variables[$variable];
    }
}
