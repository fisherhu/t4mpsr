{include file='page-head.tpl'}

<script type="text/javascript">
function SelectUser(UserName, UserID,amount){
 document.getElementById('username').innerHTML = UserName;
 document.getElementById('payvalues').style.visibility = "visible";
 document.adddonation.userid.value=UserID;
 document.adddonation.amount.value=amount;
}

function cbCheck(cb,sb) {
  document.getElementById(sb).disabled = !cb.checked
}
</script>

<div class="appcanvas">

{include file='subpage-menu.tpl'} <div class="clear"></div>

  <form name="adddonation" 
        method="post" 
        action="{$SCRIPT_NAME}" 
        style="margin:0px;padding:0px;"
        {literal}
        onSubmit="if (this.comment.value == 'comment if you like') {this.comment.value=''}; "
        {/literal}
  >
  <input type="hidden" name="menuitem" value="adddonation" />
  <input type="hidden" name="userid" value="0" />
  <div class="userlist">
  {foreach from=$tenant item="entry"}
   <div class="usernamebutton" OnClick="javascript:SelectUser('{$entry.name|escape}',{$entry.tid},{$entry.balance})">{$entry.name|escape}</div>
  {/foreach}
  </div>
  <div class="paytext"><span class="payname" id="username">No one</span> selected to donate</div>
  <div id="payvalues" style="visibility:hidden;">
  <div style="float:left;"> </div>
  <div style="display: inline-block">
  <div class="payamount">
   <input class="payinput" type="text" name="amount">
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
  <div class="clear"></div>
  <input class="payinput" id="submit" name="submit" type="submit" value="Are you sure?" disabled>
  <div class="clear"></div>
  <input type="checkbox" name="checked" onChange="cbCheck(this,'submit');"> Yes, I want to submit this donation
  </div>
</div>
  
<div class="clear"></div>
</div>
  </form>

<script type="text/javascript">
/* I hope the form already constructed now */
document.adddonation.amount.value='enter amount';
document.adddonation.comment.value='comment if you like';
document.adddonation.amount.style.color='grey';
document.adddonation.comment.style.color='grey';

</script>

{include file='page-footer.tpl'}
