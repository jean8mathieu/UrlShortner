<?php

/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/1/2016
 * Time: 8:24 PM
 */
include ("Connection.php");
class API
{
    public function insertURL($normal){
        $connection = new Connection();
        $conn = $connection->getConnection();

        $normal = html_entity_decode(trim($normal));

        if(strlen($normal) < 1)
            return json_encode(array('error' => 'true', 'result' => array('URL can not be empty')));

        if (filter_var($normal, FILTER_VALIDATE_URL) === false)
            return json_encode(array('error' => 'true', 'result' => array('URL Not Valid' . $normal)));

        if($this->checkRestrictions($normal)){
            return json_encode(array('error' => 'true', 'result' => array('URL Restricted...')));
        }


        $stmt = $conn->prepare("SELECT * FROM url WHERE url_normal = ?");
        $stmt->bindParam(1, $normal);
        $stmt->execute();

        if($stmt->rowCount() < 1){
            $short = null;
            do{
                $short = substr(md5(microtime() . $normal),rand(0,26),5);
            }while($this->checkShort($short));
            $stmt = $conn->prepare('INSERT INTO url (`url_normal`, `url_short`) VALUES (?,?)');
            $stmt->bindParam(1, $normal);
            $stmt->bindParam(2, $short);
            if($stmt->execute()){
                return json_encode(array('error' => 'false', 'result' => array('url_normal' => $normal, 'url_short' => $short)));
            }
        }else{
            $result = $stmt->fetchAll();
            $final = array();
            foreach($result as $row){
                $final = array('error' => 'false', 'result' => array('url_normal' => $row['url_normal'], 'url_short' => $row['url_short']));
            }
            return json_encode($final);
        }
        return json_encode(array('error' => 'true', 'result' => array('Please try again...')));

    }

    public function getURLLimit($limit = 100)
    {
        $connection = new Connection();
        $conn = $connection->getConnection();
        $conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

        $stmt = $conn->prepare("SELECT * ,(SELECT COUNT(*) FROM views WHERE url_short = url.url_short) as views FROM url ORDER BY url_id DESC LIMIT ? ;");
        $stmt->bindParam(1, $limit);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return json_encode(array('error' => 'false', 'result' => $result));
    }

    function checkShort($short){
        $connection = new Connection();
        $conn = $connection->getConnection();

        $stmt = $conn->prepare("SELECT * FROM url WHERE short_url = ?");
        $stmt->bindParam(1, $short);
        $stmt->execute();
        if($stmt->rowCount() > 0)
            return true;
        return false;
    }

    public function getURL($short){
        $connection = new Connection();
        $conn = $connection->getConnection();
        $stmt = $conn->prepare("SELECT * FROM url WHERE url_short = ? LIMIT 1;");
        $stmt->bindParam(1, $short);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if($stmt->rowCount() > 0){
            $stmt = $conn->prepare("INSERT INTO views (`url_short`, `ip`) VALUES(?,?)");
            $stmt->bindParam(1, $short);
            $stmt->bindParam(2, $this->getIP());
            $stmt->execute();
            return json_encode(array('error' => 'false', 'result' => $result));
        }else{
            return json_encode(array('error' => 'true', 'result' => array("URL does not exist")));
        }
    }

    private function getIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function checkRestrictions($string){
        foreach($this->restrictions as $bad){
            $place = strpos($string,$bad);
            if(!empty($place))
                return true;
        }
        return false;
    }


    private $restrictions = array(
        "porn",
        "sex",
        "xxx",
        "fuck",
        "suck",
        "redtube",
        "video-one",
        "xvideos",
        "spankbang",
        "xhamster",
        "xnxx",
        "cur.lv",
        "tinyurl",
        "jmdev",
        "chaturbate",
        "bazoocam",
        "jizz"
    );


}