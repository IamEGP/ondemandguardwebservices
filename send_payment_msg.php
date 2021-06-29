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

        $sql = "SELECT id_user_app FROM tj_requete WHERE id=$id_requete";
        $result = mysqli_query($con,$sql);
        // output data of each row
        $row = mysqli_fetch_assoc($result);
        $id_user_app = $row['id_user_app'];

        /** Start Notification **/
        $tmsg='';
        $terrormsg='';
        
        $title=str_replace("'","\'","Payment invitation");
        $msg=str_replace("'","\'","You have arrived at your destination. We kindly ask you to pay the driver. Good continuation!");
        
        $tab[] = array();
        $tab = explode("\\",$msg);
        $msg_ = "";
        for($i=0; $i<count($tab); $i++){
            $msg_ = $msg_."".$tab[$i];
        }

        $gcm = new GCM();

        $message=array("body"=>$msg_,"title"=>$title,"sound"=>"mySound","tag"=>"payment_msg");

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
        /** End Notification **/

        $response['msg']['etat'] = 1;

        echo json_encode($response);
        mysqli_close($con);
    // }
?>