<?php

class DependencyAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $template_dir = realpath(__DIR__ . "/templates");
        $template_variables = [
            '$setting.test.foo' => ['test.tpl'],
            '$setting.foo.bar' => ['foreach.tpl']
        ];
        $analyzer = new \Smartydeps\DependencyAnalyzer($template_dir, null, $template_variables, "/.+\\.tpl$/");
        $result = $analyzer->analyze();
        $this->assertEquals([], $result->getFilesDependsOn("hoge/foo.tpl"));
        $this->assertEquals(['index.tpl'], $result->getFilesDependsOn("test.tpl"));
        $this->assertEquals(['index.tpl'], $result->getFilesDependsOn("hogehoge.tpl"));

        $this->assertEquals([
            'foreach.tpl', 'hogehoge.tpl', 'test.tpl'
        ], $result->getFilesIncludedBy("index.tpl"));
        $this->assertEquals($result->getFilesIncludedBy("hoge/foo.tpl"), []);
    }
}