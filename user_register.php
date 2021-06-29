<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $prenom = $_POST['prenom'];
        $prenom = str_replace("'","\'",$prenom);
        $prenom = $_POST['prenom'];
        $phone = $_POST['phone'];
        $mdp = $_POST['mdp'];
        $mdp = str_replace("'","\'",$mdp);
        $login_type = $_POST['login_type'];
        $tonotify = $_POST['tonotify'];
        $mdp = md5($mdp);
        $date_heure = date('Y-m-d H:i:s');

        $chkemail = mysqli_query($con, "select * from tj_user_app where phone='$phone'");
        if (mysqli_num_rows($chkemail) > 0) {
            $row = $chkemail->fetch_assoc();
            
            if ($login_type != 'phone' && $row['login_type'] == $login_type) {
                $response['msg']['etat'] = 1;
                $response['msg']['message'] = "Social Login";
                unset($row['mdp']);
                $response['user'] = $row;
            } else {
                $response['msg']['etat'] = 2;
                $response['msg']['message'] = "Phone number already exist...";
            }
        } else {
                $insertdata = mysqli_query($con, "insert into tj_user_app(prenom,phone,mdp,statut,login_type,tonotify,creer)
                values('$prenom','$phone','$mdp','yes','$login_type','$tonotify','$date_heure')");
                $id = mysqli_insert_id($con);
                if ($insertdata > 0) {
                    $response['msg']['etat'] = 1;
                    
                    $get_user = mysqli_query($con, "select * from tj_user_app where id=$id");
                    $row = $get_user->fetch_assoc();
                    unset($row['mdp']);
                    $row['user_cat'] = "user_app";
                    $response['user'] = $row;
                } else {
                    $response['msg']['etat'] = 3;
                }
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>