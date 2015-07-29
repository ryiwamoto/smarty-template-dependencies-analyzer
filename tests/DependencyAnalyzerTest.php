<?php

class DependencyAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $template_dir = realpath(__DIR__ . "/templates");
        $template_variables = [
            '$setting.test.foo' => ['test.tpl']
        ];
        $analyzer = new \Smartydeps\DependencyAnalyzer($template_dir, null, $template_variables, "/.+\\.tpl$/");
        $result = $analyzer->analyze();
        $this->assertEquals($result->getFilesDependsOn("hoge/foo.tpl"), []);
        $this->assertEquals($result->getFilesDependsOn("test.tpl"), ['index.tpl']);
        $this->assertEquals($result->getFilesDependsOn("hogehoge.tpl"), ['index.tpl']);

        $this->assertEquals($result->getFilesIncludedBy("index.tpl"), [
            'hogehoge.tpl', 'test.tpl'
        ]);
        $this->assertEquals($result->getFilesIncludedBy("hoge/foo.tpl"), []);
    }
}