<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
    $con->set_charset("utf8");
    include_once 'GCM.php';
    
    // if($_SERVER['REQUEST_METHOD'] =='POST'){
        $id_conducteur = $_POST['id_conducteur'];
        $id_requete = $_POST['id_requete'];
        $image = $_POST['image'];
        $image_name = $_POST['image_name'];
        $cout = $_POST['cout'];
        $distance = $_POST['distance'];
        $duree = $_POST['duree'];
        // $id_conducteur = 2;
        // $id_requete = 44;
        $date_heure = date('Y-m-d H:i:s');

        if(!empty($image)){
            $img_name = $image_name;
            $ImagePath = "images/recu_trajet_course/$img_name";
        }else{
            $img_name = "";
        }
        $image_name = str_replace("'","\'",$image_name);

        $updatedata = mysqli_query($con, "update tj_requete set statut_course='clôturer', id_conducteur_accepter=$id_conducteur, modifier='$date_heure' where id=$id_requete");

        $sql = "SELECT r.id_user_app,u.nom,u.prenom,u.email 
        FROM tj_requete r, tj_user_app u
        WHERE r.id_user_app=u.id AND r.id=$id_requete";
        $result = mysqli_query($con,$sql);
        // output data of each row
        $row = mysqli_fetch_assoc($result);
        $id_user_app = $row['id_user_app'];
        $nom = $row['nom'];
        $prenom = $row['prenom'];
        $email = $row['email'];
        
        $selectdata = mysqli_query($con, "select id from tj_recu where id_course=$id_requete and id_conducteur=$id_conducteur and id_user_app=$id_user_app");
        
        if(!mysqli_num_rows($selectdata) > 0){
            $insertdata = mysqli_query($con, "insert into tj_recu(image,image_name,id_course,id_conducteur,id_user_app,creer,montant,duree,distance)
            values('$img_name','$image_name',$id_requete,$id_conducteur,$id_user_app,'$date_heure',$cout,'$duree',$distance)");

            if($insertdata > 0){
                if(!empty($image))
                file_put_contents($ImagePath,base64_decode($image));
            }
        }

        /** Start Notification **/
        $tmsg='';
        $terrormsg='';
        
        $title=str_replace("'","\'","Closing of the race");
        $msg=str_replace("'","\'","End of the race! We wish you an excellent result!");
        
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
        /** End Notification **/

        if($email != ""){
            $to = $email;
            $subject = "Payment receipt - Yellow Taxi";
            $message = '
                <body style="margin:100px; background: #f8f8f8; ">
                    <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">
                        <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px; background: #fff;">
                            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: top; padding-bottom:30px;" align="center">
                                            <img src="http://projets.hevenbf.com/yellow%20taxi/webservices/images/logo_taxijaune.jpg" alt="logo Taxi Jaune" style="border:none" width="15%">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="padding: 40px; background: #fff;">
                                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <img src="http://projets.hevenbf.com/yellow%20taxi/webservices/images/recu_trajet_course/'.$img_name.'" alt="logo Taxi Jaune" style="border:none" width="100%">
                                            <h3>Payment receipt </h3>
                                            <p>Hello Mr./Mrs '.$prenom.' '.$nom.'</p>
                                            <b><u>Details of your payment receipt:</u></b><br>
                                            <p><b>Distance:</b> '.$distance.' M</p>
                                            <p><b>Duration:</b> '.$duree.'</p>
                                            <p><b>Cost:</b> '.$cout.' $</p>
                                            <p><b>Date:</b> '.$date_heure.'</p>
                                            <br/>
                                            <p>Good continuation and see you soon !</p>
                                            <p>Regards Yellow Taxi</p>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </body>
            ';
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\b";
            $headers .= 'From: Taxi Jaune' . "\r\n";
            mail($to,$subject,$message,$headers);
        }

        if ($updatedata > 0) {
            $response['msg']['etat'] = 1;
        } else {
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    // }
?>