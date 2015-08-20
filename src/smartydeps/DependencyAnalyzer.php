<?php

namespace Smartydeps;

use Phine\Path\Path;

class DependencyAnalyzer
{
    /** @var string */
    private $template_dir;

    /** @var string */
    private $plugins_dir;

    /** @var IncludePathResolver */
    private $include_path_resolver;

    /** @var string */
    private $template_pattern;

    /**
     * @param array $template_dir
     * @param string $plugins_dir
     * @param array $template_variables
     * @param string $template_pattern
     */
    public function __construct($template_dir, $plugins_dir = null, $template_variables = [], $template_pattern = '/.*/')
    {
        $this->template_dir = $template_dir;
        $this->plugins_dir = $plugins_dir;
        $this->include_path_resolver = new IncludePathResolver($template_variables);
        $this->template_pattern = $template_pattern;
    }

    /**
     * @return AnalysisResult
     */
    public function analyze()
    {
        $compiled_templates = [];
        foreach ($this->template_dir as $template_dir) {
            $compiled_templates = array_merge($compiled_templates, $this->compileAllTemplates($template_dir));
        }
        return $this->getAnalysisResult($compiled_templates);
    }

    private function compileAllTemplates($template_dir)
    {
        $compiler = new Compiler($template_dir, $this->plugins_dir);
        $templates = $this->getTemplateFilesRecursive($template_dir);
        return array_map(function ($template) use ($compiler) {
            return $compiler->compile($template);
        }, $templates);
    }

    /**
     * @param string $template_dir
     * @return array
     */
    private function getTemplateFilesRecursive($template_dir)
    {
        $template_pattern = $this->template_pattern;
        $files = array();
        $itr = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($template_dir));
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

    /**
     * @param CompiledTemplate[] $compiled_templates
     * @return AnalysisResult
     */
    private function getAnalysisResult(array $compiled_templates)
    {
        $incoming = new DependencyNodes();
        $outgoing = new DependencyNodes();

        foreach ($compiled_templates as $compiled_template) {
            $template_name = $compiled_template->relative_path;
            $smarty_include_parser = new SmartyIncludeParser($this->include_path_resolver);
            $include_files = $smarty_include_parser->parse($compiled_template->compiled_content);
            foreach ($include_files as $file) {
                $incoming->add($file, $template_name);
            }
            $outgoing->add($template_name, $include_files);
        }
        return new AnalysisResult($incoming, $outgoing);
    }
}
