<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    include_once 'GCM.php';
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_user_app = $_POST['id_user_app'];
        $id_requete = $_POST['id_requete'];
        // $id_conducteur = 2;
        // $id_requete = 44;
        $date_heure = date('Y-m-d H:i:s');

        $updatedata = mysqli_query($con, "delete from tj_requete where id=$id_requete AND id_user_app=$id_user_app");

        if ($updatedata > 0) {
            $response['msg']['etat'] = 1;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>