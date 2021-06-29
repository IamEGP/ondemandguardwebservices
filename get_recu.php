<?php
	date_default_timezone_set ('Africa/Ouagadougou');
	include("query/connexion.php");
	$con->set_charset("utf8");
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_requete = $_POST['id_requete'];
        $id_user_app = $_POST['id_user_app'];

        $sql = "SELECT * FROM tj_recu WHERE id_course=$id_requete AND id_user_app=$id_user_app";
        $result = mysqli_query($con,$sql);
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            $output[] = $row;
        }
        
        if(mysqli_num_rows($result) > 0){
            $response['msg'] = $output;
            $response['msg']['etat'] = 1;
        }else{
            $response['msg']['etat'] = 2;
        }

        echo json_encode($response);
        mysqli_close($con);
    }
?>