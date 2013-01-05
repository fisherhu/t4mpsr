{include file='page-head.tpl'} 

<div class="appcanvas">

{include file='subpage-menu.tpl'}
 <div class="clear"></div>
{if {$message} != ''}{$message}{/if}
<form  name="addexpensecheck" method="post" action="{$SCRIPT_NAME}" style="padding:0px; margin:0px;">

 <div class="actualNames">
  {foreach from=$tenants item=array_item key=id}
    <div class="avName">{$array_item["name"]} pays</div>
    {/foreach}
  </div>

 <div class="actualValues">
  {foreach from=$tenants item=array_item key=id}
    <div class="avValue">{$tenantpays[$array_item["id"]]}</div>
    {/foreach}
  </div>

 <div class="note">Note:<br><textarea name="note" rows="10" cols="40"></textarea>
 <div class="clear"></div>
 Date:<br><input style="font-size:90%" size="10" type="text" name="date" value="{$date}">
 </div>


<div class="clear"></div>

<input type="hidden" name="menuitem" value="confirmexpense" />
<input type="hidden" name="checkedvalues" value="{foreach from=$tenants item=array_item key=id}{$array_item["id"]}:{$tenantpays[$array_item["id"]]};{/foreach}">

<div class="clear"></div>
<div  style="padding:30px;">
<input style="font-size:120%" type="submit" name="submit" value="checked and considered ok">
</div>
</form>
</div>

{include file='page-footer.tpl'}
