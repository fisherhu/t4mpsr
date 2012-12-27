
{include file='page-head.tpl'}

<script type="text/javascript">

function IsNumeric(n) {
 return !isNaN(parseFloat(n)) && isFinite(n);
}


 var names = [];
 var tenants = [];
 var tgroups = [];
 var tenantclicked = [];
 var groupclicked = [];
 var whichGroupClicked = 0;
 var tenantgroupmembers = [];
 var tenantgrouppay = [];
 var tenantcount  = []; /* array containing each tenant gorup population, its name should be cahnged indeed */
     /* This array will contain the compute amount each tenant have to pay */
 var computedPaymentArray = [];
 var roundfactor = {$roundfactor};
 var sameAll = false;

 function checkboxChange(cb) {
   /* if equal selected the personal payments should be cleared
    any personal payment selected again then the whole thing
    computed again                                             */
   for (i=0;i<names.length;i++) tenantclicked[i] = 0; // Tenantcliced indexed by tenant IDs
   for (i=0;i<tgroups.length;i++) groupclicked[i] = 0; // Groupclicked indexed by group IDs

   sameAll = document.getElementById(cb).checked;

   ComputePayment(document.getElementById('amount'));

/*   if (sameAll) { document.getElementById('actual').style.visibility = 'hidden' }
      else
      { document.getElementById('actual').style.visibility = 'visible' }
*/
 }

/*
http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
decimal_sep: character used as deciaml separtor, it defaults to '.' when omitted
thousands_sep: char used as thousands separator, it defaults to ',' when omitted
*/
Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep)
{ 
   var n = this,
   c = isNaN(decimals) ? 2 : Math.abs(decimals), //if decimal is zero we must take it, it means user does not want to show any decimal
   d = decimal_sep || '.', //if no decimal separator is passed we use the dot as default decimal separator (we MUST use a decimal separator)

   /*
   according to [http://stackoverflow.com/questions/411352/how-best-to-determine-if-an-argument-is-not-sent-to-the-javascript-function]
   the fastest way to check for not defined parameter is to use typeof value === 'undefined' 
   rather than doing value === undefined.
   */   
   t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, //if you don't want to use a thousands separator you can pass empty string as thousands_sep value

   sign = (n < 0) ? '-' : '',

   //extracting the absolute value of the integer part of the number and converting to string
   i = parseInt(n = Math.abs(n).toFixed(c)) + '', 

   j = ((j = i.length) > 3) ? j % 3 : 0; 
   return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 
}

   /* List of all tenants  - IDs and NAMES */
 {foreach from=$tenants item=array_item key=id}
  names[{$array_item["id"]}] = '{$array_item["name"]}';
 {/foreach}

   /* List of tenant group IDs and NAMES */
 {foreach from=$tenantgroups item=array_item key=id}
  tgroups[{$array_item["id"]}] = '{$array_item["name"]}';
 {/foreach}

   /* List of individual tenants - ID */
 {foreach from=$indiviualtenants item=array_item key=id}
  tenants[{$id}] = '{$array_item['id']}';
 {/foreach}

   /* List of tenant groups and members of each group */
 {foreach from=$tenantgroupmembers item=array_item key=id}
  tenantgroupmembers[{$id}] = [ {$array_item['tgroup']} , {$array_item['tenant']} ];
 {/foreach}

   /* array of tenant ids which one clicked */
 {foreach from=$tenants item=array_item key=id}
  tenantclicked[{$array_item["id"]}] = '0';
 {/foreach}

   /* array of tenant group ids which one clicked */
 {foreach from=$tenantgroups item=array_item key=id}
  groupclicked[{$array_item["id"]}] = '0';
 {/foreach}

{literal}

 /* some data should be ready first */
   var numIndividualTenants = tenants.length; // The tenants array contains the individual tenants a better name would be awesome
   var numTenants = 0, numTenantGroup = 0;

 /* Count how many member in each tenant group */
 for (i=0;i<tenantgroupmembers.length;i++) {
    if (null == tenantcount[tenantgroupmembers[i][0]]) {tenantcount[tenantgroupmembers[i][0]] = 0}
    tenantcount[tenantgroupmembers[i][0]]++
 }

   var i = names.length;
   while (i--) {
       if (typeof names[i] != "undefined") numTenants++ }

   var i = tenantcount.length;
   while (i--) {
       if (typeof tenantcount[i] != "undefined") numTenantGroup++ }


 /* Why names.length? Before the first click the tenantclicked array contains
    nothing. At the first click we initialise the array to match the tenants
    IDs - the names array contains the tenant ID (index) and name (value)
    pairs.
 */
 function ClickTenant(id) { // alert('click ' + id);
     for (i=0;i<names.length;i++) {
         if (( i == id ) && ((tenantclicked[i] == 0) || (tenantclicked[i] == null))) {tenantclicked[i] = 1 } else {tenantclicked[i] = 0}
     }

     /* individual tenant clicked so clear all the group clicks */
     for (i=0;i<tgroups.length;i++) groupclicked[i] = 0

     /* update the display to reflect the changes */
     ComputePayment(document.getElementById('amount'));
 }

 function ClickTenantGroup(id) {

    // Select the group is clicked, unselect else
    for (i=0;i<tgroups.length;i++) {
        if (( i == id )  && ((groupclicked[i] == 0) || (groupclicked[i] == null))) {
           groupclicked[i] = 1
        } else {
           groupclicked[i] = 0
        }
    } // for

    // groupclicked is set, lets see the tenants, first clear any tenant click
    for (i=0;i<names.length;i++) tenantclicked[i] = 0;

    // All clear now set only those belongs this group
    for (i=0;i<tenantgroupmembers.length;i++)
        if ((tenantgroupmembers[i][0] == id ) && (groupclicked[id] == 1))
            tenantclicked[tenantgroupmembers[i][1]] = 1;

    ComputePayment(document.getElementById('amount')); // alert(groupclicked + ' - ' + tenantclicked);
 }

 function ComputePayment(field) {

   // It is a bloated one so do some tidy. Variable definitions first.

   // How much to pay?
   amount=parseFloat(String(field.value));

   // var tGroupPopulation = [];
   var GroupPay, GroupsPay = 0;
   var tNames = '';  /* This will contains the innerhtml for the tenant list */
   var tGroups = ''; /* This will contains the innerhtml for the tenant group list */
   var ThisTenantPays = 0;
   var IndividualTenantsPay = 0, PaySum = 0, PrepaidSum = 0;
   var PayAmount = '';
   var PayMath = '';
   var paymentString = '';

   /* Count how many tenants selected if any */
   var clickedNum = 0, lastClickedID = 0;


  // The variables are defined, initialise some others

  // If any tenant has been clicked just count them.
  for (i=0;i<tenantclicked.length;i++) {
      if (tenantclicked[i] == 1 ) { clickedNum++ ; lastClickedID = i; }
  }

  /*
     Compute how much each teant and tenant group member should pay.
     Dividing and rounding done once and forever to avoid any rounding
     problem could emerge later.

     At first: easy case  everyone pays the same amount
   */
   if (sameAll) { var payPerTenant = Math.round(roundfactor * amount / numTenants) / roundfactor ;
   } else {
     /*
        tenants and tenant groups because if not sameAll then the tenant groups are counts as one tenant
        so 100 = 50 + [ 25 + 25 ]
     */
     var payPerTenant = Math.round(roundfactor * amount / (numIndividualTenants + numTenantGroup)) / roundfactor;

     // How much the each group members' should pay payPerTenant divided by the group population
     for (i=1;i<tenantcount.length; i++) {
         tenantgrouppay[i] = Math.round(roundfactor * payPerTenant / tenantcount[i]) / roundfactor;
     }
  } // end if-sameall


  /* First print the individual tenants' name and amount to be paid */
  for (i=0;i<tenants.length; i++) {
      if (tenantclicked[tenants[i]] == 1) { // The ID of tenant stored in the tenants array.
         // The tenant should have refund
         var Refund = payPerTenant - amount;
         PrepaidSum = payPerTenant;
         computedPaymentArray[tenants[i]]=Refund;
//    alert(computedPaymentArray);
         ThisTenantPays = 0; // pays zero because already paid when bought the stuff.
         PayAmount='<span class="paid">('+ Refund.toMoney()+ ')</span>'; // The refund should be displayed
  } else {
     ThisTenantPays = payPerTenant;
     PaySum = PaySum + ThisTenantPays;
     IndividualTenantsPay = IndividualTenantsPay + payPerTenant;
     PayAmount='<div class="tAmount">-' + ThisTenantPays.toMoney() +'</div>';
     computedPaymentArray[tenants[i]] = payPerTenant;
  }

     // Add new row to the tenant list //
     tNames = tNames + '<div class="tName" OnClick="ClickTenant(' + tenants[i] + ');">' +
                       names[tenants[i]] + '</div>' +
                       PayAmount +
                       '<div style="clear:both;"></div>'
  }

  // Tenant list to display //
  tNames = '<div class="tNames">' + tNames + '</div><div style="clear:both;"></div>';
  document.getElementById('tenants').innerHTML = tNames;


  // So far so good, the crappiest part follow however the idea is the same

  /* Individual tenants printed, now show the tenant groups */
  for (i=0;i<tgroups.length; i++) { // Walk through the tenant groups
     var gName = tgroups[i];
     var gMemberList = '';
     var GroupSum = 0;

     for (j=0;j<tenantgroupmembers.length;j++) { // Wander along the tenant group - member pairs

     if (tenantgroupmembers[j][0] == i) { // The tenant belongs the current tenant group.

        // The tenantgrouppay contains payment value for each group
        PayPerTenant = tenantgrouppay[i];

        // The second element of the subarray is the user ID
        if (tenantclicked[tenantgroupmembers[j][1]] == 1) { // Tenant clicked
           /*
            Now is the fun part, a tenant group member is clicked. This could
            mean two things:
               - the tenant bought stuff so the tenant gets refunded
               - the tenant group bought the stuff so they all gets refunded

            At first lets see if only a tenant group member should get the refund
           */
           if ( clickedNum == 1 ) { // Only one clicked.
              Refund = tenantgrouppay[i] - amount;
              PrepaidSum = PrepaidSum + tenantgrouppay[i];
           } else { // Not one, so the group pays
              // var PrepaidPerTenant = Math.round(roundfactor * amount / tenantcount[i]) * roundfactor;
              // Refund goes to the group, so multiply the prepaid amount with the number of tenants
              Refund = Math.round( roundfactor * (tenantgrouppay[i] - amount) / tenants[i]) / roundfactor;
              var GroupRefund = payPerTenant - amount;
              Refund = Math.round( roundfactor * GroupRefund / tenants[i]) / roundfactor;
              // PrepaidSum = PrepaidSum + (PrepaidPerTenant * tenantcount[i]);
              PrepaidSum = PrepaidSum + tenantgrouppay[i];
           }
           ThisTenantPays = 0 ;
           computedPaymentArray[tenantgroupmembers[j][1]] = Refund;
           PayAmount='<span class="paid">' + Refund.toMoney() + '</span>';
           } else { // Tenant is NOT clicked
             if (sameAll) {
                ThisTenantPays = payPerTenant;
                computedPaymentArray[tenantgroupmembers[j][1]] = ThisTenantPays;
             } else {
               ThisTenantPays = tenantgrouppay[i];
               computedPaymentArray[tenantgroupmembers[j][1]] = ThisTenantPays;
             };

           PayAmount='<div class="tAmount">-' + ThisTenantPays.toMoney() +'</div>';
           PaySum = PaySum + ThisTenantPays; GroupSum = GroupSum + ThisTenantPays;
           } // END OF Tenant is NOT clicked


           gMemberList = gMemberList + '<div class="tName gmember" OnClick="ClickTenant(' +
                         tenantgroupmembers[j][1] + ');">' +
                         String(names[tenantgroupmembers[j][1]]) +
                         '</div>' +
                         PayAmount +
                         '<div style="clear:both;"></div>';
        } // END OF The tenant belongs the current tenant group.
     }

     if ( gMemberList != '' ) {
        gMemberList = '<div class="tNames">' + gMemberList + '</div>';
        tGroups = tGroups + 
                  '<div class="tgroupName" OnClick=ClickTenantGroup("' + i + '");>' +
                  String(gName) + ' (' + GroupSum.toMoney() + ')</div>' +
                  gMemberList +
                  '<div style="clear:both;"></div>' ;
     }
     document.getElementById('tenants').style.visibility = 'visible';
     document.getElementById('groups').style.visibility = 'visible';

  }


  // load the html to it's place
  if ( tGroups != '' ) {
     document.getElementById('groups').innerHTML =  tGroups;
  }


   // Update the final amount
   var grandSum = PaySum + PrepaidSum
   PayMath = '<div title="SUM" class="mathline">' + PaySum.toMoney() + '</div>' + '<div style="clear:both;"></div>' +
             '<div style="border-bottom:1px solid black"><div class="plus">+</div><div class="mathline">' + PrepaidSum.toMoney() + '</div>' + '<div style="clear:both;"></div></div>' +
             '<div class="mathline">' + grandSum.toMoney() + '</div>';
   document.getElementById('actual').innerHTML = PayMath;


   // Assemble the payment info for php
   for (i=0;i<computedPaymentArray.length;i++) {
       paymentString = paymentString + String(i) + ':' + String(computedPaymentArray[i]) + ';';
   }
   document.addexpense.payinfo.value=paymentString;
  }

{/literal}

</script>


<div class="appcanvas">

{include file='subpage-menu.tpl'}
 <div class="clear"></div>
  <div class="iform">
  <form  name="addexpense" method="post" action="{$SCRIPT_NAME}" style="padding:0px; margin:0px;">
  <input type="hidden" name="menuitem" value="addexpensecheck" />
  <input type="hidden" name="tenant" value="0" />
  <input type="hidden" name="payinfo">
  <input type="text" size=10 id="amount" name="amount" value="enter amount"
          {literal}
          onkeyup="if (IsNumeric(this.value)) { document.addexpense.submit.disabled=false};  ComputePayment(this);"
          onfocus="if (this.value == 'enter amount')	
                   {this.value = ''; this.style.color='inherit';}"
          {/literal}>
  <div><input type="checkbox" id="eq" value="eq" OnChange="checkboxChange('eq')"> everyone pays equally</div>
  <input type="submit" name="submit" value="process" style="font-size:100%; float:right;">
  </form></div><div class="actual"  id="actual"></div>
  <div id="comm" class="comm">

  <div style="clear:both;"></div>

  <!-- place for the individual tenant names -->  <div id="tenants"  class="tenants"></div>
  <div style="clear:both;"></div>

  <!-- place for the individual tenant names -->  <div id="groups" class="groups"></div>
  <div style="clear:both;"></div>

  </div>
<div style="clear:both;"></div>
</div>

{include file='page-footer.tpl'}

<script type="text/javascript">
/* I hope the form already constructed now */
document.addexpense.amount.value='enter amount';
document.addexpense.amount.style.color='grey';
document.addexpense.submit.disabled='true';
</script>