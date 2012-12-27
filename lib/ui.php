<?php

////////////////////////////////////////////////////////////////////////
/**
* Class to generate the main menu data for the
* app main page.
**/
class t4mpsrMainMenu {

/**
* It does not display anything but prepares the menu text and commands
* @return An array object containing a menu item name and text
**/
 function GetEntries() {

 global $lang;

 return $MMEntries = array(
    "listbalance" => sprintf($lang['MENU_LIST_BALANCE']),
    "listtenants" => sprintf($lang['MENU_LIST_TENANTS']),
    "addpayment"  => sprintf($lang['MENU_ADD_PAYMENT']),
    "addexpense"  => sprintf($lang['MENU_ADD_EXPENSE']),
    "adddonation" => sprintf($lang['MENU_ADD_DONATION']),
    "edittenants" => sprintf($lang['MENU_EDIT_TENANTS'])
    );
 }

}

////////////////////////////////////////////////////////////////////////
/**
* Class to create the main page
*
* The main page assembled from
* - The main menu @see t4mpsrMainMenu
* The MainMenu on the MainPage is a way larger representation
* of the same data (menu entries) displayed in each page.
**/
class t4mpsrMainPage {

 function __construct() {
    // instantiate the template object
    $this->tpl = new t4mpsr_smarty;

   // Load main menu
   $t4mpsrMM = new t4mpsrMainMenu;

   // Draws the MainMenu for the MainPage
   $this->tpl->assign('data', $t4mpsrMM->GetEntries());
   $this->tpl->assign('time', time());
   $this->tpl->display('mainpage.tpl');
 }
}

////////////////////////////////////////////////////////////////////////
/**
* Class to create the tenant list page
*
* The main page assembled from
* - The menu list (t4mpsrMainMenu)
* - The tenant list (t4mpsrTenants)
**/
class t4mpsrTenantsPage {

/// Some object init
 function __construct() {
    // instantiate the template object
    $this->tpl = new t4mpsr_smarty;

    // Initialise MainMenu and Tenants objects
    $this->t4mpsrMM = new t4mpsrMainMenu;
    $this->t4mpsrTenants = new t4mpsrTenants;
}


/**
* Draws a tenant list with balance.
*

* @see t4mpsrMainMenu::GetEntries()
* @see t4mpsrTenants::GetTenantList()
*
**/
function ListTenantBalance() {
    // Process template
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetTenantBalanceList());
    $this->tpl->assign('time', time());
    $this->tpl->display('tenantpage.tpl');
 }

function AddExpense() {
    // Process template
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('roundfactor', ROUND_FACTOR);
    $this->tpl->assign('tenants', $this->t4mpsrTenants->GetTenantList());
    $this->tpl->assign('tenantgroups', $this->t4mpsrTenants->GetTenantGroupList());
    $this->tpl->assign('indiviualtenants', $this->t4mpsrTenants->GetIndividualTenants());
    $this->tpl->assign('tenantgroupmembers', $this->t4mpsrTenants->GetTenantGroups());
    $this->tpl->assign('time', time());
    $this->tpl->display('addexpense.tpl');
 }

function AddExpenseCheck($PaymentInfo) {
    // Unfold records
    $tenantpays = array();
    $records=explode(';', $PaymentInfo);
    foreach ($records as $tenantRecord) {
      $tenantArray=explode(':', $tenantRecord);
      // clean up the data and force to be int and float
      $index = (int) $tenantArray[0];
      $value = (float) $tenantArray[1];
      $tenantpays[$index] = $value;
    }
    // var_dump($_POST);
    $date=date('Y-m-d');
    $this->tpl->assign('date', $date);
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenants', $this->t4mpsrTenants->GetTenantList());
    $this->tpl->assign('tenantpays', $tenantpays);
    $this->tpl->assign('time', time());
    $this->tpl->display('addexpensecheck.tpl');
 }

function ConfirmExpense($PostData) {
    $expenseData=$PostData["checkedvalues"];
    $expenseDate=$PostData["date"];
    $expenseNote=$PostData["note"];
    // echo $expenseDate;
    $this->t4mpsrTenants->SaveExpenses($expenseData, $expenseDate, $expenseNote);
    $this->tpl->assign('message', 'Expenses added');
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetTenantBalanceList());
    $this->tpl->assign('time', time());
    $this->tpl->display('tenantpage.tpl');
 }

function AddPayment() {
    // Process template
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetTenantList());
    $this->tpl->assign('date', date('Y-m-d'));
    $this->tpl->assign('time', time());
    $this->tpl->display('addpayment.tpl');
 }

function AddDonation($PostData) {
     // var_dump($PostData);
    if (isset($PostData["submit"])) {
       $amount=$PostData["amount"];
       $tid=$PostData["userid"];
       $note=$PostData["comment"];
       // print $amount . '   --  ' .$tid . ' -- ' . $note . '<br>';
       $this->t4mpsrTenants->AddDonation($tid,$amount,$note);
       $message="Donation sent";
    }
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetTenantBalanceList());
    $this->tpl->assign('date', date('Y-m-d'));
    $this->tpl->assign('time', time());
    $this->tpl->display('adddonation.tpl');
 }

function AddPaymentCheck($PostData) {
    // Process template
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $TenantName = $this->t4mpsrTenants->GetTenantNameByID($PostData['userid']);
    $this->tpl->assign('userid', $PostData['userid']);
    $this->tpl->assign('username', $TenantName);
    $this->tpl->assign('amount', $PostData['amount']);
    $this->tpl->assign('pdate', $PostData['pdate']);
    $this->tpl->assign('comment', $PostData['comment']);
    $this->tpl->assign('time', time());
    $this->tpl->display('addpaymentcheck.tpl');
 }


function ConfirmPayment($PostData) {
    $id=$PostData['payid'];
    $amount=$PostData['payamount'];
    $date=$PostData['paydate'];
    $comment=$PostData['paycomment'];
    $this->t4mpsrTenants->InsertPayment($id,$amount,$date,$comment);

    $this->tpl->assign('message', 'Payment done');
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetTenantBalanceList());
    $this->tpl->assign('time', time());
    $this->tpl->display('tenantpage.tpl');
 }

function EditTenants() {
    // Process template
    $this->tpl->assign('data', $this->t4mpsrMM->GetEntries());
    $this->tpl->assign('tenant', $this->t4mpsrTenants->GetAllTenantList());
    $this->tpl->assign('tenantgroup', $this->t4mpsrTenants->GetTenantGroupList());
    $this->tpl->assign('time', time());
    $this->tpl->assign('dropdown', DRPDWN);
    $this->tpl->display('edittenantpage.tpl');
 }

}

?>