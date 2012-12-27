{* smarty *}

  {foreach from=$tenant item="entry"}
     <div style="background-color:{cycle values="#317390,#416380"}">
      <div class="userid">{$entry.tid}</div>
      <div class="username">{$entry.name|escape}</div>
      {if $entry.balance lt 0}
      <div class="negbalance">{$entry.balance}</div>
      {else}
      <div class="balance">{$entry.balance}</div> 
      {/if}
      <div style="clear: both;"></div>
     </div>
  {/foreach}

