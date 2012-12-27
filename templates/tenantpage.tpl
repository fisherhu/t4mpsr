{include file='page-head.tpl'} 

<div class="appcanvas">

{include file='subpage-menu.tpl'}
 <div class="clear"></div>
{if $message ne ''}{$message}{/if}
{include file='list-tenants.tpl'}

</div>

{include file='page-footer.tpl'}
