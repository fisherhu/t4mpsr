{include file='page-head.tpl'}


<div class="appcanvas">


{include file='subpage-menu.tpl'} <div class="clear"></div>

<form name="checkform" method="post" action="{$SCRIPT_NAME}">

<input type="hidden" name="menuitem" value="payconfirm" />
<input type="hidden" name="payid" value="{$userid}"/>
<input type="hidden" name="payamount" value="{$amount}" />
<input type="hidden" name="paydate" value="{$pdate}" />
<input type="hidden" name="paycomment" value="{$comment}" />


<div class="checkenvelope">
<div>Please check:</div>
<table border=0 style="font-size:28px" cellpadding="15px">
<tr>
<td>Tenant:</td><td style="color:#bbbbbb">{$username} (id: {$userid})</td>
</tr><tr>
<td>Amount:</td><td style="color:#bbbbbb">{$amount}</td>
</tr><tr>
<td>Date:</td><td style="color:#bbbbbb">{$pdate}</td>
</tr><tr>
<td>Comment:</td><td style="color:#bbbbbb">{$comment}</td>
</tr></table>
</div>
<input class="payinput" name="submit" type="submit" value="Checked and agreed">
</form>
{include file='page-footer.tpl'}
