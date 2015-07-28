<?php

namespace Smartydeps;

class Compiler
{
    /** @var string */
    private $template_dir;

    /** @var string[] */
    private $plugins_dir;

    /**
     * @param string $template_dir full path for template directory
     * @param string $plugins_dir
     */
    public function __construct($template_dir, $plugins_dir)
    {
        $this->template_dir = $template_dir;
        $this->plugins_dir = $plugins_dir;
    }

    /**
     * @param string $template_path template file. (full path)
     * @return CompiledTemplate
     */
    public function compile($template_path)
    {
        $compiler = new \Smarty_Compiler();
        if ($this->plugins_dir) {
            $compiler->plugins_dir[] = $this->plugins_dir;
        }
        $compiler->caching = 1;
        $source_content = file_get_contents($template_path);
        $compiled_content = '';
        $compiler->_compile_file($template_path, $source_content, $compiled_content);
        $smarty_file_name = $compiler->_get_auto_filename($this->template_dir, $template_path);
        return new CompiledTemplate($template_path, $this->template_dir, $smarty_file_name, $compiled_content);
    }
}
