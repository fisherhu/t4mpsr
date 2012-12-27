{include file='page-head.tpl'}

<script type="text/javascript">
function SelectUser(UserName, UserID){
 document.getElementById('username').innerHTML = UserName;
 document.getElementById('payvalues').style.visibility = "visible";
 document.addpayment.userid.value=UserID;
}

function IsNumeric(n) {
 return !isNaN(parseFloat(n)) && isFinite(n);
}
</script>

<div class="appcanvas">

{include file='subpage-menu.tpl'} <div class="clear"></div>

  <form name="addpayment" 
        method="post" 
        action="{$SCRIPT_NAME}" 
        style="margin:0px;padding:0px;"
        {literal}
        onSubmit="if (this.comment.value == 'comment if you like') {this.comment.value=''}; "
        {/literal}
  >
  <input type="hidden" name="menuitem" value="addpaymentcheck" />
  <input type="hidden" name="userid" value="0" />
  <div class="userlist">
  {foreach from=$tenant item="entry"}
   <div class="usernamebutton" OnClick="javascript:SelectUser('{$entry.name|escape}',{$entry.id})">{$entry.name|escape}</div>
  {/foreach}
  </div>
  <div class="paytext"><span class="payname" id="username">No one</span> selected to pay</div>
  <div id="payvalues" style="visibility:hidden;">
  <div style="float:left;"> </div>
  <div style="display: inline-block">
  <div class="payamount">
   <input class="payinput"
          type="text"
          name="amount"
          {literal}
          onblur="if (this.value == '')
                  {this.value = 'enter amount'; this.style.color='grey';}
                  else
                  { if (IsNumeric(this.value)) { document.addpayment.submit.disabled=false} };"

          onfocus="if (this.value == 'enter amount') 
                   {this.value = ''; this.style.color='inherit';}
                   else
                   {document.addpayment.submit.disabled=true};"
          {/literal}
    >
  </div>
  <div class="payamount"><input class="payinput" type="text" size="20" name="pdate" value="{$date}"></div>

  <div class="payamount">
   <input class="payinput"
          type="text"
          size="20"
          name="comment"
          maxlength="30"
          {literal}
          onfocus="if (this.value == 'comment if you like')
                   {this.value = ''; this.style.color='inherit';}"
          {/literal}
   >
  </div>

  <input class="payinput"  name="submit" type="submit" value="Check values" disabled>
  </div>
</div>
  
<div class="clear"></div>
</div>
  </form>

<script type="text/javascript">
/* I hope the form already constructed now */
document.addpayment.amount.value='enter amount';
document.addpayment.comment.value='comment if you like';
document.addpayment.amount.style.color='grey';
document.addpayment.comment.style.color='grey';

</script>

{include file='page-footer.tpl'}
