<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_user = $_POST['id_user'];
        $prenom = $_POST['prenom'];
        $prenom = str_replace("'","\'",$prenom);
        $date_heure = date('Y-m-d H:i:s');

        $updatedata = mysqli_query($con, "update tj_user_app set prenom='$prenom', modifier='$date_heure' where id=$id_user");

        if ($updatedata > 0) {
            $response['msg']['etat'] = 1;
            $response['msg']['prenom'] = $prenom;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>