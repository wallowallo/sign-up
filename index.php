<script>
function RegisterUser()
{
  if(!isset($_POST['submitted']))
  {
     return false;
  }

  $formvars = array();

  if(!$this->ValidateRegistrationSubmission())
  {
      return false;
  }

  $this->CollectRegistrationSubmission($formvars);

  if(!$this->SaveToDatabase($formvars))
  {
      return false;
  }

  if(!$this->SendUserConfirmationEmail($formvars))
  {
      return false;
  }

  $this->SendAdminIntimationEmail($formvars);

  return true;
}
</script>
<script>
function SaveToDatabase(&$formvars)
 {
     if(!$this->DBLogin())
     {
         $this->HandleError("Database login failed!");
         return false;
     }
     if(!$this->Ensuretable())
     {
         return false;
     }
     if(!$this->IsFieldUnique($formvars,'email'))
     {
         $this->HandleError("This email is already registered");
         return false;
     }

     if(!$this->IsFieldUnique($formvars,'username'))
     {
         $this->HandleError("This UserName is already used. Please try another username");
         return false;
     }
     if(!$this->InsertIntoDB($formvars))
     {
         $this->HandleError("Inserting to Database failed!");
         return false;
     }
     return true;
 }
 </script>
 <script>
 function CreateTable()
{
  $qry = "Create Table $this->tablename (".
          "id_user INT NOT NULL AUTO_INCREMENT ,".
          "name VARCHAR( 128 ) NOT NULL ,".
          "email VARCHAR( 64 ) NOT NULL ,".
          "phone_number VARCHAR( 16 ) NOT NULL ,".
          "username VARCHAR( 16 ) NOT NULL ,".
          "password VARCHAR( 32 ) NOT NULL ,".
          "confirmcode VARCHAR(32) ,".
          "PRIMARY KEY ( id_user )".
          ")";

  if(!mysql_query($qry,$this->connection))
  {
      $this->HandleDBError("Error creating the table \nquery was\n $qry");
      return false;
  }
  return true;
}
</script>
<script>
function InsertIntoDB(&$formvars)
{
  $confirmcode = $this->MakeConfirmationMd5($formvars['email']);

  $insert_query = 'insert into '.$this->tablename.'(
          fname,
          lname,
          email,
          password,
          gender
          )
          values
          (
          "' . $this->SanitizeForSQL($formvars['name']) . '",
          "' . $this->SanitizeForSQL($formvars['email']) . '",
          "' . $this->SanitizeForSQL($formvars['username']) . '",
          "' . md5($formvars['password']) . '",
          "' . $confirmcode . '"
          )';
  if(!mysql_query( $insert_query ,$this->connection))
  {
      $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
      return false;
  }
  return true;
}
</script>
