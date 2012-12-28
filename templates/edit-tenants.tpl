{* smarty *}

<script type="text/javascript">

function ChangeInput(ElementID){
    FormData='' +
    '<input type="text" name="name" id="new_user_name" style="margin:0px;padding:0px;font-size:28px;"/>' +
    '<label style="padding-left:30px;">Tenant group</label><input id="tg_checkbox" type="checkbox" name="type" value="tg">'

    document.getElementById(ElementID).innerHTML = FormData;
    document.getElementById('plus').style.visibility='hidden';
}

function popUp(tkey) {

  window.prompt('Ctrl-C top copy the tenant key:', tkey);

}
</script>

  <!-- one global form because I made no proper plan first -->
  <form name="usermod" method="post" action="{$SCRIPT_NAME}" style="margin:0px;padding:0px;">
  <input type="hidden" name="menuitem" value="modtenant" />

  <!-- aye aye ugly hack to hide a command into a hidden input -->
  <input type="hidden" name="deluser" value="0" />
  <input type="hidden" name="pauser" value="0" />

     <div  class="newuserrow">
      <div OnClick="javascript:ChangeInput('newuser')" class="useradd" id="plus">+</div>
      <div id="newuser" class="newusername">Add new tenant or tenant group.</div>

      <div style="clear:both;"></div>
     </div>


  {foreach from=$tenant item="entry"}
     <div style="background-color:{cycle values="#317390,#416380"}">
      <!-- OnClick modify the deluser value to the userid desired to delete then submit the form -->
      <div class="userdel" OnClick="document.usermod.deluser.value={$entry.id};if (confirm('Are you sure you want to submit the form?')) { document.usermod.submit(); };">
      -
      </div>

       {if {$entry.active} eq 1}
       <div class="username" OnClick="document.usermod.pauser.value={$entry.id};document.usermod.submit();">{$entry.name|escape}</div> 
       {else}
       <div class="usernameinactive" OnClick="document.usermod.pauser.value={$entry.id};document.usermod.submit();">{$entry.name|escape} <span style="font-size:50%">(inactive)</span></div> 
       {/if}

      <div class="balance">

       <select name="{$dropdown}{$entry.id}">
        <option value="0">No tenant group</option>
        {foreach from=$tenantgroup item="tgroup"}
         {if {$tgroup.id} eq {$entry.jointid}}
          <option selected="selected" value="{$tgroup.id}">{$tgroup.name}</option>
         {else}
           <option value="{$tgroup.id}">{$tgroup.name}</option>
         {/if}
        {/foreach}
       </select>

      </div>
       <div class="checkmark" OnClick="document.usermod.submit();">
        &#10003;
       </div>
       <div class="checkmark" OnClick="popUp('{$entry.key}');">
        &#9990
       </div>

      <div style="clear: both;"></div>
     </div>
  {/foreach}
</form>
