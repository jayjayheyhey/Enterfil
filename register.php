<?php 

include 'connect.php';

if(isset($_POST['signIn'])){
   $email=$_POST['email'];
   $password=$_POST['password'];
   
   $sql="SELECT * FROM users WHERE email='$email' and password='$password'";
   $result=$conn->query($sql);
   if($result->num_rows>0){
    session_start();
    $row=$result->fetch_assoc();
    $_SESSION['email']=$row['email'];
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