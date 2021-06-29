<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	// include("query/fonction.php");
    $con->set_charset("utf8");
    include_once 'GCM.php';
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $message = $_POST['message'];;
        $message = str_replace("'","\'",$message);
        $id_envoyeur = $_POST['id_envoyeur'];
        $id_receveur = $_POST['id_receveur'];
        $user_cat = $_POST['user_cat'];
        $id_requete = $_POST['id_requete'];
        $date_heure = date('Y-m-d H:i:s');
        
        $date_heure = date('Y-m-d H:i:s');
        if($user_cat == "user_app"){
            $query = "INSERT INTO tj_message(message,id_user_app,id_conducteur,id_requete,creer,user_cat)
            VALUES('$message',$id_envoyeur,$id_receveur,$id_requete,'$date_heure','$user_cat')";

            $sql = "SELECT m.id,m.message,m.user_cat,m.id_requete,m.id_user_app,m.id_conducteur,m.creer,u.nom,u.prenom,c.nom as nomConducteur, c.prenom as prenomConducteur
            FROM tj_message m, tj_user_app u, tj_conducteur c
            WHERE m.id_user_app=u.id AND m.id_conducteur=c.id AND m.id_requete=$id_requete AND m.id_user_app=$id_envoyeur ORDER BY m.id DESC LIMIT 1";
        }else{
            $query = "INSERT INTO tj_message(message,id_user_app,id_conducteur,id_requete,creer,user_cat)
            VALUES('$message',$id_receveur,$id_envoyeur,$id_requete,'$date_heure','$user_cat')";

            $sql = "SELECT m.id,m.message,m.user_cat,m.id_requete,m.id_user_app,m.id_conducteur,m.creer,u.nom,u.prenom,c.nom as nomConducteur, c.prenom as prenomConducteur
            FROM tj_message m, tj_user_app u, tj_conducteur c
            WHERE m.id_user_app=u.id AND m.id_conducteur=c.id AND m.id_requete=$id_requete AND m.id_conducteur=$id_envoyeur ORDER BY m.id DESC LIMIT 1";
        }
        $insertdata = mysqli_query($con, $query);
        
        $result = mysqli_query($con,$sql);
        // output data of each row
        $row = mysqli_fetch_assoc($result);
        
        /** Start Notification **/
        $tmsg='';
        $terrormsg='';
        
        if($user_cat == "user_app"){
            $title=str_replace("'","\'",$row['prenom'].' '.$row['nom']);
        }else{
            $title=str_replace("'","\'",$row['prenomConducteur'].' '.$row['nomConducteur']);
        }
        $msg=str_replace("'","\'",$message);
        
        $tab[] = array();
        $tab = explode("\\",$msg);
        $msg_ = "";
        for($i=0; $i<count($tab); $i++){
            $msg_ = $msg_."".$tab[$i];
        }

        $gcm = new GCM();

        $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>$id_receveur."_".$id_envoyeur."_".$id_requete."_".$row['prenom']." ".$row['nom']);

        if($user_cat == "user_app"){
            $query = "select fcm_id from tj_conducteur where fcm_id<>'' and id=$id_receveur";
        }else{
            $query = "select fcm_id from tj_user_app where fcm_id<>'' and id=$id_receveur";
        }
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
        /** End Notification **/

        if ($insertdata > 0) {
            $response['msg'] = $row;
            $response['msg']['etat'] = 1;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>