<?php

namespace Smartydeps;

class AnalysisResult
{
    /** @var DependencyNodes */
    private $incoming;

    /** @var DependencyNodes */
    private $outgoing;

    /** @var string[] */
    private $all_files = [];

    /**
     * @param DependencyNodes $incoming
     * @param DependencyNodes $outgoing
     */
    public function __construct($incoming, $outgoing)
    {
        $this->incoming = $incoming;
        $this->outgoing = $outgoing;
        $all_files = array_unique(array_merge($incoming->files(), $outgoing->files()));
        $this->all_files = $all_files;
    }

    /**
     * @param string $name
     * @return \string[]
     */
    public function getFilesIncludedBy($name)
    {
        return $this->outgoing->get($name);
    }

    /**
     * @param string $name
     * @return string[]
     */
    public function getFilesDependsOn($name)
    {
        return $this->incoming->get($name);
    }
}