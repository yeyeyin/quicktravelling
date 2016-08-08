
<?php
/**
 * 操作数据库mysql 的类，完成增删改查的操作
 *
 */
class ms_new_mysql {

    private $dbHost;
    private $dbUser;
    private $dbPassword;
    private $dbTable;
    private $dbConn;
    private $result;
    private $sql;
    private $pre;
    private $coding;
    private $pcon;
    private $queryNum;
    private static $daoImpl;

    public function __construct($dbHost, $dbUser, $dbPassword, $dbTable, $pre, $coding, $pcon) {
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbTable = $dbTable;
        $this->pre = $pre;
        $this->coding = $coding;
        $this->pcon = $pcon;
        $this->connect();
        $this->select_db($dbTable);
    }
/*
    public function __clone() {
        return self::$daoImpl;
    }

    public static function getDaoImpl($linkConfig) {

        if (empty(self::$daoImpl)) {
            self::$daoImpl = new ms_new_mysql($linkConfig['db']['dbhost'], $linkConfig['db']['dbuser'], $linkConfig['db']['dbpw'], $linkConfig['db']['dbname'], $linkConfig['db']['tablepre'], $linkConfig['db']['dbcharset'], $linkConfig['db']['pconnect']);
        }
        return self::$daoImpl;
    }
*/
    public function connect() {
        $func = $this->pcon == 1 ? "mysqli_pconnect" : "mysqli_connect";

        //$this->dbConn = @$func($this->dbHost, $this->dbUser, $this->dbPassword);
        $this->dbConn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPassword, $this->pcon,3307);
        if (!$this->dbConn) {
            $this->halt("不能链接数据库", $this->sql);
            return false;
        }
        if ($this->version() > '4.1') {
            $serverset = $this->coding ? "character_set_connection='$this->coding',character_set_results='$this->coding',character_set_client=binary" : '';
            $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',') . " sql_mode='' ") : '';
            $serverset && mysqli_query($this->dbConn, "SET $serverset");
        }
        return $this->dbConn;
    }

    /**
     * 选择一个数据库
     *
     * @param string $dbTable
     * @return boolean
     */
    public function select_db($dbTable) {
        if (!@mysqli_select_db($this->dbConn, $dbTable)) {
            $this->halt("没有" . $dbTable . "这个数据库");
            return false;
        } else {
            $this->dbTable = $dbTable;
            return true;
        }
    }

    /**
     * 查询语句，传过来一个sql，执行，如果语句正确，返回结果集资源
     *
     * @param string $sql
     * @param string $type
     * @return Resouce query
     */
    public function query($sql, $type = '') {

        if ($query = mysqli_query($this->dbConn, $sql)) {
            $this->queryNum++;
            return $query;
        } else {
            $this->halt("Mysql 查询出错", $sql);
            return false;
        }
    }

    /**
     * 插入一组数据或一条数据
     *
     * @param string $tableName
     * @param boolean
     */
    public function insert($tableName, $info) {

        $this->checkFields($tableName, $info);
        $insert_sql = "INSERT INTO `$tableName`(`" . implode('`,`', array_keys($info)) . "`) VALUES('" . implode("','", $info) . "')";
        return $this->query("$insert_sql");
    }

    /**
     * 更细数据库中的数据
     *
     * @param string $tableName
     * @param array $info
     * @param string $where
     * @return boolean
     */
    public function update($tableName, $info, $where = '') {
        $this->checkFields($tableName, $info);
        if ($where) {
            $sql = '';

            foreach ($info as $k => $v) {
                $sql .= ", `$k`='$v'";
            }
            $sql = substr($sql, 1);

            $sql = "UPDATE `$tableName` SET $sql WHERE $where";
        } else {
            $sql = "REPLACE INTO `$tableName`(`" . implode('`,`', array_keys($info)) . "`) VALUES('" . implode("','", $info) . "')";
        }
        return $this->query($sql);
    }

    /**
     * 检查一个字段是否在这张表中存在
     *
     * @param string $tableName
     * @param array $array
     * @return message
     */
    public function checkFields($tableName, $array) {

        $fields = $this->getFields($tableName);

        foreach ($array as $key => $val) {
            if (!in_array($key, $fields)) {
                $this->halt("Mysql 错误", "找不到" . $key . "这个字段在" . $tableName . "里面");
                return false;
            }
        }
    }

    /**
     * 获取一张表中的所有字段
     *
     * @param string $tableName
     * @return array fileds
     */
    public function getFields($tableName) {
        $fields = array();
        $result = $this->query("SHOW COLUMNS FROM `$tableName`");
        while ($list = $this->fetchArray($result)) {
            $fields[] = $list['Field'];
        }
        $this->freeResult($result);
        return $fields;
    }

    /**
     * 释放当前数据库结果集的内存
     *
     * @param Resource $result
     * @return null
     */
    public function freeResult($result) {
        return @mysqli_free_result($result);
    }

    /**
     * 使用while 可以迭代输出 一个结果集中的所有数据
     *
     * @param Resouce $query
     * @param result type $result_type
     * @return array
     */
    public function fetchArray($query, $result_type = MYSQL_ASSOC) {
        return mysqli_fetch_array($query, $result_type);
    }

    public function getAll($sql){
        $i = 0;
        $result = array();
        $temp = $this->query($sql);
        while($row=$this->fetchArray($temp))
            $result[++$i] = $row;
        return $result;
    }

    /**
     * 返回一个结果集中的一条数据
     *
     * @param $sql $query
     * @param string $type
     * 
     * 
     */
    public function getOne($sql, $type = '', $expires = 3600) {
        $query = $this->query($sql, $type, $expires);
        $rs = $this->fetchArray($query);
        $this->freeResult($rs);
        return $rs;
    }

    /**
     * 获取插入的数据的返回的insetid
     *
     * @return insetid
     */
    public function insertId() {
        return mysqli_insert_id($this->dbConn);
    }

    /**
     * 获取当前的结果集中存在多少条数据
     *
     * @param string $query
     * @return int nums
     */
    public function numRows($query) {
        return @mysql_numrows($query);
    }

    /**
     * 获取当前的结果集中，有多少个字段
     *
     * @param Resouce $query
     * @return int fields nums
     */
    public function numFields($query) {
        return @mysqli_num_fields($query);
    }

    /**
     * 获取当前执行的sql总条数
     *
     * @return queryNum
     */
    public function getQueryNum() {
        return $this->queryNum;
    }

    /**
     * 获取当前文件中的函数,传入一个当前类存在函数，单例调用
     *
     * @param unknown_type $funcname
     * @param unknown_type $params
     * @return unknown
     */
    public function getFunc($funcname, $params = '') {
        if (empty($params)) {
            return $this->$funcname();
        } else {
            return $this->$funcname($this->getFuncParams($params));
        }
    }

    /**
     * 如果是一个数组，那么拼接起来，处理返回一个参数集合
     *
     * @param array,string $params
     * @return string a,d,3
     */
    public function getFuncParams($params) {
        $returnStr = "";
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $returnStr .= $val . ",";
            }
            return rtrim($returnStr, ",");
        } else {
            return $params;
        }
    }

    /**
     * 获取当前数据库的版本信息
     *
     * @return version
     */
    public function version() {
        return mysqli_get_server_info($this->dbConn);
    }

    /**
     * 获取当前mysql数据的报错号
     *
     * @return errorno
     */
    public function errno() {
        return intval(@mysqli_errno($this->dbConn));
    }

    /**
     * 获取当前数据库的 提示信息
     *
     * @return unknown
     */
    public function error() {
        return @mysqli_error($this->dbConn);
    }

    /**
     * 操作数据库出错，提示信息
     *
     * @param unknown_type $message
     * @param unknown_type $sql
     */
    function halt($message = '', $sql = '') {
        $this->errormsg = "<b>MySQL Query : </b>$sql <br /><b> MySQL Error : </b>" . $this->error() . " <br /> <b>MySQL Errno : </b>" . $this->errno() . " <br /><b> Message : </b> $message";
        exit($this->errormsg);
    }

    function showTable() {
        $tables = array();
        $result = $this->query("SHOW TABLES");
        while ($list = $this->fetchArray($result)) {
            $tables[] = $list['Tables_in_' . $this->dbTable];
        }
        $this->freeResult($result);
        return $tables;
    }

}

// $dbObj = new ms_new_mysql("w.rdc.sae.sina.com.cn", "1zj255xzoo","m4j2m5l0443xmmi2l0izzhmklkjkij02h02423j2", "app_quicktravelling", "", "utf-8", "app_quicktravelling");
$dbObj = new ms_new_mysql("localhost", "root", "", "tourist", "", "utf-8", "tourist");
    mysqli_query($dbObj->connect(),"set character set 'utf8'");//读库 
    mysqli_query($dbObj->connect(),'set names utf8');

?>
