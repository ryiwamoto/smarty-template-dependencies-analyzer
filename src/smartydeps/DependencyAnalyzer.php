<?php

namespace Smartydeps;

use Phine\Path\Path;

class DependencyAnalyzer
{
    /** @var string */
    private $template_dir;

    /** @var IncludePathResolver */
    private $include_path_resolver;

    /** @var Compiler */
    private $compiler;

    /** @var string */
    private $template_pattern;

    /**
     * @param string $template_dir
     * @param string $plugins_dir
     * @param array $template_variables
     * @param string $template_pattern
     */
    public function __construct($template_dir, $plugins_dir = null, $template_variables = [], $template_pattern = '/.*/')
    {
        $this->template_dir = $template_dir;
        $this->compiler = new Compiler($template_dir, $plugins_dir);
        $this->include_path_resolver = new IncludePathResolver($template_dir, $template_variables);
        $this->template_pattern = $template_pattern;
    }

    /**
     * @return array
     */
    public function analyze()
    {
        $templates = $this->getTemplateFilesRecursive($this->template_pattern);
        $compiled_templates = $this->compileAllTemplates($templates);
        return $this->getDependencyLists($compiled_templates);
    }

    /**
     * @param string $template_pattern
     * @return array
     */
    private function getTemplateFilesRecursive($template_pattern)
    {
        $files = array();
        $itr = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->template_dir));
        foreach ($itr as $file) {
            if ($file->isDir()) {
                continue;
            }
            $path = Path::canonical($file->getPathname());
            if (preg_match($template_pattern, $path)) {
                $files[] = $path;
            }
        }
        return $files;
    }

    private function compileAllTemplates($templates)
    {
        return array_map(function ($template) {
            return $this->compiler->compile($template);
        }, $templates);
    }

    /**
     * @param CompiledTemplate[] $compiled_templates
     * @return array
     */
    private function getDependencyLists(array $compiled_templates)
    {
        $dependency_nodes = [];
        foreach ($compiled_templates as $compiled_template) {
            $smarty_include_parser = new SmartyIncludeParser($this->include_path_resolver);
            $to = $smarty_include_parser->parse($compiled_template->compiled_content);
            $dependency_node = new DependencyNode($compiled_template->relative_path, $to);
            $dependency_nodes[] = $dependency_node;
        }
        return $dependency_nodes;
    }
}
