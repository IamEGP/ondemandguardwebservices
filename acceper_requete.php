<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    include_once 'GCM.php';
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_conducteur = $_POST['id_conducteur'];
        $id_requete = $_POST['id_requete'];
        // $id_conducteur = 2;
        // $id_requete = 44;
        $date_heure = date('Y-m-d H:i:s');

        $chkuser = mysqli_query($con, "select id,id_conducteur,id_user_app from tj_requete where statut='en cours' and id=$id_requete");
        if (mysqli_num_rows($chkuser) > 0) {
            $id_conducteur_table = "";
            $id_user_app = "";
            while($row = mysqli_fetch_assoc($chkuser)) {
                $id_conducteur_table = $row['id_conducteur'];
                $id_user_app = $row['id_user_app'];
            }

            $updatedata = mysqli_query($con, "update tj_requete set statut='accepter', statut_course='en cours', id_conducteur_accepter=$id_conducteur, modifier='$date_heure' where id=$id_requete");
    
            if ($updatedata > 0) {
                $response['msg']['etat'] = 1;

                $sql = "SELECT id FROM tj_conducteur WHERE id IN ($id_conducteur_table)";
                $result = mysqli_query($con,$sql);
                // output data of each row
                while($row = mysqli_fetch_assoc($result)) {
                    $output[] = $row;
                }

                // $j=0;
                // while($j<5 && $j<count($output)){
                //     $id_conducteur = $output[$j]['id'];
                    
                    /** Start Notification des autres conducteurs **/
                    $tmsg='';
                    $terrormsg='';
                    
                    $title=str_replace("'","\'","Race validation");
                    $msg=str_replace("'","\'","One of your colleagues has just validated a request");
                    
                    $tab[] = array();
                    $tab = explode("\\",$msg);
                    $msg_ = "";
                    for($i=0; $i<count($tab); $i++){
                        $msg_ = $msg_."".$tab[$i];
                    }

                    $gcm = new GCM();

                    $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>"unknown");

                    $query = "select fcm_id from tj_conducteur where fcm_id<>'' and id in ($id_conducteur_table) and id != $id_conducteur";
                    $result = mysqli_query($con, $query);

                    $tokens = array();
                    if (mysqli_num_rows($result) > 0) {
                        while ($user = $result->fetch_assoc()) {
                            if (!empty($user['fcm_id'])) {
                                $tokens[] = $user['fcm_id'];
                            }
                        }
                    }
                    $temp = array();
                    if (count($tokens) > 0) {
                        $gcm->send_notification($tokens, $message, $temp);
                    }
                    /** End Notification des autres conducteurs **/

                    /** Start Notification de l'utilisateur **/
                    $tmsg='';
                    $terrormsg='';
                    
                    $title=str_replace("'","\'","Validation of your request");
                    $msg=str_replace("'","\'","A driver has just validated your request. Please provide him with the necessary information.");
                    
                    $tab[] = array();
                    $tab = explode("\\",$msg);
                    $msg_ = "";
                    for($i=0; $i<count($tab); $i++){
                        $msg_ = $msg_."".$tab[$i];
                    }

                    $gcm = new GCM();

                    $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>"mes_requetes");

                    $query = "select fcm_id from tj_user_app where fcm_id<>'' and id=$id_user_app";
                    $result = mysqli_query($con, $query);

                    $tokens = array();
                    if (mysqli_num_rows($result) > 0) {
                        while ($user = $result->fetch_assoc()) {
                            if (!empty($user['fcm_id'])) {
                                $tokens[] = $user['fcm_id'];
                            }
                        }
                    }
                    $temp = array();
                    if (count($tokens) > 0) {
                        $gcm->send_notification($tokens, $message, $temp);
                    }
                    /** End Notification de l'utilisateur **/

                    // $j++;
                // }

            } else {
                $response['msg']['etat'] = 2;
            }
        }else{
            $response['msg']['etat'] = 3;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>