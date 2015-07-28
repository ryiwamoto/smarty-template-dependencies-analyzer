<?php
namespace Smartydeps;

class DependencyNode
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string[]
     */
    public $to;


    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}
