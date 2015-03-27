<?php
function getAdminId() {
    $app = \Slim\Slim::getInstance();
    $app->response->headers->set('Content-Type', 'application/json');
    $admin_id = $_SESSION['loginUsername'];
    $app->response->setBody(json_encode(array("admin_id"=>$admin_id)));
}

function checkCurrentPassword() {
    try {
        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'application/json');
        $request = $app->request();
        $admin_id = $_SESSION['loginUsername'];
        $hashPassword = json_decode($request->getBody())->hashPassword;
        $hashPassword = sha1($admin_id.$hashPassword);

    } catch(Exception $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
        return;
    }
    
    try {
        $db = new DBManager();
        $sql = "SELECT COUNT(1) AS cnt from ADMIN_USERS a 
            INNER JOIN STUDENT b ON a.user_id = b.student_id
            WHERE a.user_id = '".$admin_id."' AND b.password = '".$hashPassword."'";
        $result = $db->getData($sql);
        $response_arr = array();
        if ($result){
            while($row = sqlsrv_fetch_array($result)){
                $cnt = $row['cnt'];
                $app->response->setBody(json_encode(array("status"=>"success", "cnt"=>"$cnt", "sql"=>"$sql")));
            }
        }
        $db = null;

    } catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }
}

function updatePassword() {
    try {
        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'application/json');
        $request = $app->request();
        $admin_id = $_SESSION['loginUsername'];
        $hashPassword = json_decode($request->getBody())->hashPassword;
        $hashPassword = sha1($admin_id.$hashPassword);

    } catch(Exception $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"input", "reason"=>$e->getMessage()))));
        return;
    }
    
    try {
        $db = new DBManager();
        $sql = "UPDATE STUDENT SET password = '$hashPassword', updatedate = GETDATE() WHERE student_id = '$admin_id'";
        $db->beginSet();
        if($db->setData($sql))
        {
            $db->commitWork();
            $app->response->setBody(json_encode(array("status"=>"success")));
        }
        else
        {
            $db->rollbackWork();
            $app->response->setBody(json_encode(array("status"=>"fail")));
            $app->response->write(json_encode($db->errmsg()));      
        }
        $db = null;

    } catch(PDOException $e) {
        $app->response->setBody(json_encode(array("error"=>array("source"=>"SQL", "reason"=>$e->getMessage()))));
        return;
    }
}

?>