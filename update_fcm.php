<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    $response=array();

    $user_id = $_POST['user_id'];
    $fcm_id=$_POST['fcm_id'];
    $device_id=$_POST['device_id'];
    $user_cat = $_POST['user_cat'];

    if($user_cat == "user_app"){
        $update_query = mysqli_query($con, "UPDATE tj_user_app SET fcm_id='$fcm_id',device_id='$device_id' where id=$user_id ");
        if($update_query){
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 0;
        }
    }else{
        $update_query = mysqli_query($con, "UPDATE tj_conducteur SET fcm_id='$fcm_id',device_id='$device_id' where id=$user_id ");
        if($update_query){
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 0;
        }
    }

    echo json_encode($response);
    mysqli_close($con);
?>
