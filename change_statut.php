<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_conducteur = $_POST['id_conducteur'];
        $online = $_POST['online'];

        $updatedata = mysqli_query($con, "update tj_conducteur set online='$online' where id=$id_conducteur");

        $response['msg']['online'] = $online;
        $response['msg']['etat'] = 1;

        echo json_encode($response);
        mysqli_close($con);
    // }
?>