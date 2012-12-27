
<script language="JavaScript" type="text/javascript">
<!--
function getsupport ( menuaction )
{
  document.menuform.menuitem.value = menuaction ;
  document.menuform.submit() ;
}
-->
</script>

<form name="menuform" method="post" action="{$SCRIPT_NAME}">
<input type="hidden" name="menuitem" />
<div class="mainmenu">
<ul>
{foreach from=$data key="action" item="text"}

<li>
 <div class="outerContainer">
  <div class="innerContainer">
   <div class="menutext"><a href="javascript:getsupport('{$action}')">{$text}</a></div>
  </div>
 </div>
</li>

{/foreach}
</ul>
</div>
</form>
