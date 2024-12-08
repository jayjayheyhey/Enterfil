<?php 

include 'connect.php';

if(isset($_POST['signIn'])){
   $username=$_POST['username'];
   $password=$_POST['password'];
   
   $sql="SELECT * FROM credentials_tb WHERE username='$username' and password='$password'";
   $result=$conn->query($sql);
   if($result->num_rows>0){
    session_start();
    $row=$result->fetch_assoc();
    $_SESSION['username']=$row['username'];
    header("Location: homepage.php");
    exit();
   }
   else{
      header("Location: index.php?error=1");
      exit();
    exit();
   }

}
?>