<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_user = $_POST['id_user'];
        $phone = $_POST['phone'];
        $phone = str_replace("'","\'",$phone);
        $date_heure = date('Y-m-d H:i:s');

        $updatedata = mysqli_query($con, "update tj_user_app set phone='$phone', modifier='$date_heure' where id=$id_user");

        if ($updatedata > 0) {
            $response['msg']['etat'] = 1;
            $response['msg']['phone'] = $phone;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>