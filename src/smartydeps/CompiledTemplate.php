<?php
namespace Smartydeps;

class CompiledTemplate
{
    /**
     * full path of template
     */
    public $full_path;

    /** @var string */
    public $base_path;

    /** @var string */
    public $relative_path;

    /** @var string */
    public $smarty_file_path;

    /**
     * compiled php code
     */
    public $compiled_content;

    public function __construct($full_path, $base_path, $smarty_file_path, $compiled_content)
    {
        $this->full_path = $full_path;
        $this->base_path = $base_path;
        $this->relative_path = trim(substr($full_path, strlen($base_path)), DIRECTORY_SEPARATOR);
        $this->smarty_file_path = $smarty_file_path;
        $this->compiled_content = $compiled_content;
    }
}
