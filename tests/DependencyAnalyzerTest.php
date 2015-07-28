<?php

class DependencyAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $template_dir = realpath(__DIR__ . "/templates");
        $template_variables = [
            '$setting.test.foo' => ['test']
        ];
        $analyzer = new \smartydeps\DependencyAnalyzer($template_dir, null, $template_variables, "/.+\\.tpl$/");
        $result = $analyzer->analyze();
        $this->assertEquals("hoge/foo.tpl", $result[0]->from);
        $this->assertEquals(0, count($result[0]->to));

        $this->assertEquals("index.tpl", $result[1]->from);
        $this->assertEquals(["hogehoge.tpl", 'test'], $result[1]->to);
    }
}