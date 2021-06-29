<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    $response = array();

    if($_SERVER['REQUEST_METHOD'] =='POST'){
        $date_heure = date('Y-m-d H:i:s');
        $id_user = "";
        $mdp = md5($_POST['mdp']);
        $telephone = $_POST['phone'];
        $mdp = str_replace("'","\'",$mdp);
        $telephone = str_replace("'","\'",$telephone);
        $checkuser = mysqli_query($con, "select * from tj_user_app where phone='$telephone'");
        if (mysqli_num_rows($checkuser)) {
            $checkaccount = mysqli_query($con, "select * from tj_user_app where phone='$telephone' and statut='yes'");
            if (mysqli_num_rows($checkaccount)) {
                $checkpwd = mysqli_query($con, "select * from tj_user_app where phone='$telephone' and mdp='$mdp'");
                if (mysqli_num_rows($checkpwd)) {
                    $response['msg']['etat'] = 1;
                    $response['msg']['message'] = "Success";
                    $row = $checkuser->fetch_assoc();
                    unset($row['mdp']);
                    $row['user_cat'] = "user_app";
                    $row['online'] = "";
                    $id_user = $row['id'];

                    /** A décommenter pour le déploiement **/
                    /*$ip = $_SERVER['REMOTE_ADDR']; 
                    $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
                    if($query && $query['status'] == 'success') {
                        $pays = $query['country'];
                        $ville = $query['city'];
                        $region = $query['regionName'];
                        $ip_adress = $query['query'];
                    } else {
                        $pays = "";
                        $ville = "";
                        $region = "";
                    }
                    
                    $sql = "INSERT INTO pha_connexion(pays,ville,region,ip_adress,id_user,date_connexion)
                    VALUE('$pays', '$ville', '$region', '$ip_adress', $id_user, '$date_heure')";
                    mysqli_query($con,$sql);*/

                    $response['user'] = $row;
                } else {
                    $response['msg']['etat'] = 2;
                }
            } else {
                $response['msg']['etat'] = 3;
            }
        } else {
            $checkuser = mysqli_query($con, "select * from tj_conducteur where phone='$telephone'");
            if (mysqli_num_rows($checkuser)) {
                $checkaccount = mysqli_query($con, "select * from tj_conducteur where phone='$telephone' and statut='yes'");
                if (mysqli_num_rows($checkaccount)) {
                    $checkpwd = mysqli_query($con, "select * from tj_conducteur where phone='$telephone' and mdp='$mdp'");
                    if (mysqli_num_rows($checkpwd)) {
                        $response['msg']['etat'] = 1;
                        $response['msg']['message'] = "Success";
                        $row = $checkuser->fetch_assoc();
                        unset($row['mdp']);
                        $row['user_cat'] = "conducteur";
                        $id_user = $row['id'];

                        /** A décommenter pour le déploiement **/
                        /*$ip = $_SERVER['REMOTE_ADDR']; 
                        $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
                        if($query && $query['status'] == 'success') {
                            $pays = $query['country'];
                            $ville = $query['city'];
                            $region = $query['regionName'];
                            $ip_adress = $query['query'];
                        } else {
                            $pays = "";
                            $ville = "";
                            $region = "";
                        }
                        
                        $sql = "INSERT INTO pha_connexion(pays,ville,region,ip_adress,id_user,date_connexion)
                        VALUE('$pays', '$ville', '$region', '$ip_adress', $id_user, '$date_heure')";
                        mysqli_query($con,$sql);*/

                        $response['user'] = $row;
                    } else {
                        $response['msg']['etat'] = 2;
                    }
                } else {
                    $response['msg']['etat'] = 3;
                }
            }else{
                $response['msg']['etat'] = 0;
            }
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>