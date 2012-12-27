<?php
// require('Smarty.class.php');

/**
* Class to handle tenant related functions.
*
* Class functions:
* - database modification
* - file management (if any)
*/
class t4mpsrTenants {
  /// PDO database object
  var $pdo = null;
  /// smarty template object
  var $tpl = null;
  /// error messages
  var $error = null;

  /* set database settings here! */
  /* absolutely wrong place anyway but this is in the example */
  /// PDO database type
  var $dbtype = 'mysql';
  /// PDO database name
  var $dbname = 't4mpsr';
  /// PDO database host
  var $dbhost = '172.16.234.4';
  /// PDO database username
  var $dbuser = 't4mpsr';
  /// PDO database password
  var $dbpass = 't4mpsr.pwd.%';

 /**
  * Class constructor.
  *
  * During the initialisation:
  * - instantiate the pdo object
  */
  function __construct() {

    // instantiate the pdo object
    try {
      $dsn = "{$this->dbtype}:host={$this->dbhost};dbname={$this->dbname}";
      $this->pdo =  new PDO($dsn,$this->dbuser,$this->dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      die();
    }

  }

  /**
  * Retrieves the tenant and balance list from the database
  * @return The tenant list
  */
  function GetTenantBalanceList() {
    try {
      foreach($this->pdo->query("
select
          t.id as tid
	, t.name
	, coalesce(i.amount, 0) - coalesce(p.amount, 0) as balance
from tenants as t
left join (
	select tid, sum(amount) as amount
	from expenses
	group by 1
) as p
on t.id = p.tid
left join (
	select tid, sum(amount) as amount
	from incomes
	group by 1
) as i
on t.id = i.tid
") as $row)
      $rows[] = $row;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return $rows;
  }


  function GetTenantBalanceByID($id) {
    try {
      foreach($this->pdo->query("select sum(incomes.amount) -  (select sum(expenses.amount) from expenses where tid=$id) as bal from incomes where tid=$id;") as $row)
      $rows[] = $row;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return $row['bal'];
  }



  /**
  * Retrieves the tenant and balance list from the database
  * @return The tenant list
  */
  function GetTenantList() {
    try {
      foreach($this->pdo->query("select * from tenants where active=true;") as $row)
      $rows[] = $row;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return $rows;
  }

  function GetAllTenantList() {
    try {
      foreach($this->pdo->query("select * from tenants;") as $row)
      $rows[] = $row;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return $rows;
  }

  function GetTenantNameByID($id) {
   $tnt=$this->pdo->prepare("select name from tenants where id=$id;");
   $tnt->execute();
   $result = $tnt->fetch(PDO::FETCH_ASSOC);
   return $result['name'];
  }


  /**
  * Modify the tenant groups based on the raw POST data which looks like this:
  * <PRE>
  * array(6) {
  *            ["menuitem"]=> string(9) "addtenant" 
  *            ["dropdown1"]=> string(1) "0"
  *            ["dropdown2"]=> string(1) "2"
  *            ["dropdown3"]=> string(1) "2"
  *            ["dropdown4"]=> string(1) "0"
  *            ["dropdown5"]=> string(1) "0"
  * }
  *</PRE>
  * or whatewer a DRPDWN has been set.
  * Yes, this function has plenty room to improve but ATM I don't care
  * because in normal circumstates it has no real effect.
  **/
  function ModifyTenantGroupsByPost($PostArray) {
    // new empty array
    $FilteredArray = array();

    // pattern to find the uneccessary 'dropdown' string
    $patt='/' . DRPDWN . '\d+/';

    // create a new array containing the user id, tenant group id pairs only.
    foreach ( $PostArray as $k => $v ) {
      if (preg_match($patt, $k)) $FilteredArray[substr($k,strlen(DRPDWN))] = $v;
    }

    // good we have the pure data now check its validity and process
    foreach ( $FilteredArray as $uid => $tgid ) {

      // count if at least two member of a group and the group is other than zero
      // because zero means no group
      if (count(preg_grep('/^' . $tgid . '$/', $FilteredArray)) > 1 && $tgid > 0) {

         // user $uid is member of $tgid - some error handling would be nice anway
         $db = $this->pdo->query("update tenants set jointid=$tgid where id=$uid;");
         $db->execute();
      } else {
         // The group in question now has one or no members so
         $db = $this->pdo->query("update tenants set jointid=0 where id=$uid;");
         $db->execute();
      }
    }
  }

  /**
  * Retrieves the tenant groups' list from the database
  * @return The tenant geoups' list
  */
  function GetTenantGroupList() {
    try {
      foreach($this->pdo->query("select * from tenant_groups;") as $row)
      $rows[] = $row;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return $rows;
  }


  /**
  * Inserts a new tenant into the database.
  *
  * By default any new tenant is active,
  * and not member of any tenant group.
  *
  * @param $TenantName the tenant name (what a surprise)
  */
   function addTenant($TenantName) {
    try {
      $rh = $this->pdo->prepare("insert into tenants (name,active) values ('$TenantName', 1)");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return true;
  }

   function InsertPayment($iid,$iamount,$idate,$icomment) {
    try {
      $rh = $this->pdo->prepare("insert into incomes (tid,amount,datetime,comment) values ($iid,$iamount,'$idate','$icomment')");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return true;
  }

   function addDonation($tid,$amount,$note) {
    $date=date('Y-m-d');
    // $rh = $this->pdo->beginTransaction();
     //  print $tid . $date . $amount . $note;
      try {
        $rh = $this->pdo->prepare("insert into spares (iid,datetime,amount,comment) values ($tid,'$date',$amount,'$note');");
        $rh->execute();
        // echo $rh->debugDumpParams();
      } catch (PDOException $e) {
        print "Error!: " . $e->getMessage();
       return false;
      }
      try {
        $amount= -1 * $amount;
        $rh = $this->pdo->prepare("insert into incomes (tid,datetime,amount,comment) values ($tid,'$date',$amount,'donated to spare');");
        $rh->execute();
        // echo $rh->debugDumpParams();
      } catch (PDOException $e) {
        print "Error!: " . $e->getMessage();
       return false;
      }
    // return $this->pdo->commit();
    return true;
  }



   function SaveExpenses($expenseData,$expenseDate,$expenseNote) {

    // $rh = $this->pdo->beginTransaction();

    $expenseRecords=explode(';',$expenseData);
    $rh = $this->pdo->prepare("insert into expenses (tid,amount,datetime,comment) values (:tid, :amount, :date, :note);");
    $rh->bindParam(':tid', $tenantID, PDO::PARAM_STR);
    $rh->bindParam(':amount', $value, PDO::PARAM_STR);
    $rh->bindParam(':date', $expenseDate, PDO::PARAM_STR);
    $rh->bindParam(':note', $expenseNote, PDO::PARAM_STR);
    // echo $expenseDate;
    foreach ($expenseRecords as $record) {
      $recordData=explode(':',$record);
      // var_dump($recordData);
      $tenantID=(int) $recordData[0];
      $value=(float) $recordData[1];
      try {
        $rh->execute();
      } catch (PDOException $e) {
        print "Error!: " . $e->getMessage();
       return false;
      }
      }
    // $rh->commit();
    return true;
  }


   function GetIndividualTenants() {
    try {
      $rh = $this->pdo->prepare("select id from tenants where jointid=0 and active=true;");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return $rh->fetchall(PDO::FETCH_ASSOC);
  }

   function GetTenantGroups() {
    try {
      $rh = $this->pdo->prepare("select tenants.id as tenant,
                                        tenant_groups.id tgroup
                                         from
                                        tenants, tenant_groups
                                         where
                                        tenants.jointid = tenant_groups.id and tenants.active=true;");
      $num = $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return $rh->fetchall(PDO::FETCH_ASSOC);
  }


  /**
  * Deletes a tenant from the database.
  *
  * Since the proper tables are have foreign key constraint
  * and on delete cascade, removing any tenant from the db
  * automatically removes all the tenant's transfers.
  *
  * @param $TenantName the tenant name (what a surprise)
  */
   function delTenant($TenantID) {
    try {
      $rh = $this->pdo->prepare("delete from tenants where id=$TenantID;");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return true;
  }

   function getKeyInfo($key) {
    // Get all tenants including the inactive ones
    $tl = $this->GetAllTenantList();
    // var_dump($key);
    // var_dump($tb);

    foreach ($tl as $tenant) {
      $id = $tenant['id'];
      $name = $tenant['name'];
      $str = $id . $name . T4MPSR_SALT;
      $hash = md5($str);
      // print md5($str) . "\n" . $key . "\n";
      // var_dump(md5($tr));
      if ($key == $hash) {
         $tb = $this->GetTenantBalanceByID($id);
         // var_dump($tb);
         // Print the user balance, 1st line
         print $tb . "\n";
         }
      }
    return true;
  }

  /**
  *
  * @param $TenantName the tenant name (what a surprise)
  */
   function flipTenant($TenantID) {
    try {
      $rh = $this->pdo->prepare("update tenants set active = active xor 1 where id=$TenantID;");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;

    }
    return true;
  }

  /**
  * Inserts a new tenant group into the database.
  *
  * @param $TenantGroupName the tenant group name
  */
   function addTenantGroup($TenantGroupName) {

    // purge unused group first
     $rh = $this->pdo->prepare("delete from tenant_groups where id not in (select distinct jointid from tenants);");
     $rh->execute();

    try {
      $rh = $this->pdo->prepare("insert into tenant_groups (name) values('$TenantGroupName')");
      $rh->execute();
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage();
      return false;
    }
    return true;
  }
}
?>
