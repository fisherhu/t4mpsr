<?php

header("Cache-Control: no-cache, must-revalidate");
header('Content-type: text/html; charset=utf-8');

require_once('config.php');
require_once('lib/tenants.php');
require_once('lib/ui.php');

// var_dump($_POST);
/// set the current action, if no action set display the main menu
$_action = isset($_POST['menuitem']) ? $_POST['menuitem'] : 'mainpage';

switch ($_action) {
    case 'confirmexpense':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->ConfirmExpense($_POST);
        break;
    case 'addexpense':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->AddExpense();
        break;
    case 'addexpensecheck':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $PaymentInfo = $_POST['payinfo'];
        $t4mpsrTenants->AddExpenseCheck($PaymentInfo);
        break;
    case 'addpayment':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->AddPayment();
        break;
    case 'adddonation':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->AddDonation($_POST);
        break;
    case 'addpaymentcheck':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        // var_dump($_POST);
        $t4mpsrTenants->AddPaymentCheck($_POST);
        break;
    case 'payconfirm':
        // display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->ConfirmPayment($_POST);
        break;
    case 'listtenants':
        /// display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->ListTenantBalance();
        break;
    case 'modtenant':

        // This part should be re-re-re-thinked
        // several times to clean up it a little
//         var_dump($_POST);
        // Init some objects we will rock you.
        $t4mpsrTenantList = new t4mpsrTenantsPage;
        $t4mpsrTenant = new t4mpsrTenants;

        // If no user to delete process the new user and
        // usermod parts
        if ($_POST['deluser'] === '0') {

            // If no user to pause/activate then go on
            // yes, a case should be used here instead
            // this braindead if-if-if nest. Or something.
            // I know it is crappy.
            if ($_POST['pauser'] === '0') {

                if (isset($_POST['type'])) {
                    // 'type' checkbox set so it is a tenant group
                    if (isset($_POST['name'])) {
                        // do not add empty ones
                        $t4mpsrTenant->AddTenantGroup($_POST['name']);
                    }
                } else {
                    // no type set so it is a tenant
                    if (isset($_POST['name'])) {
                        // do not add empty ones
                        $t4mpsrTenant->AddTenant($_POST['name']);
                    }
                    // So far so good the user part of the POST is processed.
                    // Now process the tenant group part.
                    $t4mpsrTenant->ModifyTenantGroupsByPost($_POST);
                }
            } else { // pauser
                $t4mpsrTenant->flipTenant($_POST['pauser']);
            }
        } else { //deluser
            $t4mpsrTenant->DelTenant($_POST['deluser']);
        }
        $t4mpsrTenantList->EditTenants();
        break;
    case 'edittenants':
        /// display the tenant table
        $t4mpsrTenants = new t4mpsrTenantsPage;
        $t4mpsrTenants->EditTenants();
        break;
    case 'remoteinfo':
        /// display the tenant table
        $t4mpsrTenant = new t4mpsrTenants;
        $key = $_POST['key'];
        $t4mpsrTenant->getKeyInfo($key);
        break;
    case 'mainpage':
    default:
        $t4mpsrMainPage = new t4mpsrMainPage;
        break;
}
?>
