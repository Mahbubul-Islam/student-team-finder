<?php
require_once('../../controllers/authCheck.php');

echo $_SESSION['user']['name'];        
echo $_SESSION['user']['email'];       
echo $_SESSION['user']['role'];        
echo $_SESSION['user']['user_id'];     
echo $_SESSION['user']['profile_image']; 
?>