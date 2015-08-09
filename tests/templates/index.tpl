{include file="hogehoge.tpl"}
{include file=$setting.test.foo}
<h1>HOGE!!!</h1>

{foreach from=$setting.foo item=f}
  {foreach from=$f.bar item=name}
    {include file=$name}
  {/foreach}
{/foreach}
