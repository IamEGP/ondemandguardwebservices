<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_user = $_POST['id_user'];
        $user_cat = $_POST['user_cat'];
        $anc_mdp = $_POST['anc_mdp'];
        $anc_mdp = str_replace("'","\'",$anc_mdp);
        $anc_mdp = md5($anc_mdp);
        $new_mdp = $_POST['new_mdp'];
        $new_mdp = str_replace("'","\'",$new_mdp);
        $new_mdp = md5($new_mdp);
        $date_heure = date('Y-m-d H:i:s');

        if($user_cat == "user_app"){
            $chkuser = mysqli_query($con, "select id from tj_user_app where mdp='$anc_mdp' AND id=$id_user");
            if (mysqli_num_rows($chkuser) > 0) {
                $updatedata = mysqli_query($con, "update tj_user_app set mdp='$new_mdp', modifier='$date_heure' where id=$id_user");
        
                if ($updatedata > 0) {
                    $response['msg']['etat'] = 1;
                } else {
                    $response['msg']['etat'] = 2;
                }
            }else{
                $response['msg']['etat'] = 3;
            }
        }else{
            $chkuser = mysqli_query($con, "select id from tj_conducteur where mdp='$anc_mdp' AND id=$id_user");
            if (mysqli_num_rows($chkuser) > 0) {
                $updatedata = mysqli_query($con, "update tj_conducteur set mdp='$new_mdp', modifier='$date_heure' where id=$id_user");
        
                if ($updatedata > 0) {
                    $response['msg']['etat'] = 1;
                } else {
                    $response['msg']['etat'] = 2;
                }
            }else{
                $response['msg']['etat'] = 3;
            }
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>