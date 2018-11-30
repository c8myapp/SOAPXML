<?php

class database {

    VAR $PDO;

    /**
     * 
     * @param String $db_type type database (server, MYSQL)
     * @param String $db_host ip database
     * @param String $db_name database used
     * @param Int $db_port port database
     * @param Sting $db_user user login database
     * @param String $db_pass password login database
     */
    function __construct($db_type, $db_host, $db_name, $db_port, $db_user, $db_pass) {
        $this->PDO = new PDO($db_type . ':host=' . $db_host . ';dbname=' . $db_name . '; port=' . $db_port, $db_user, $db_pass);
        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 
     * @param String $table table name
     * @param Array $data key => field database, value => insert value data
     * @return Mixed Integer = row affected, String HTML error 
     */
    function _insert($table, $data = array()) {
        try {
            ksort($data);
            $fieldname = implode(',', array_keys($data));
            $fieldvalue = ':' . implode(', :', array_keys($data));

            $query = 'INSERT INTO ' . $table . ' (' . $fieldname . ') VALUES (' . $fieldvalue . ')';
            $statement = $this->PDO->prepare($query);

            foreach ($data as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }

            $statement->execute();
            return $statement->rowCount();

            $statement = null;
            $this->PDO = null;
        } catch (Exception $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    /**
     * 
     * @param String $table table name
     * @param Array $data key => field database, value => insert value data
     * @param type $conditionfield key => field database, value => where value data
     * @return Mixed Integer = row affected, String HTML error 
     */
    function _update($table, $data = array(), $conditionfield = array()) {
        try {

            ksort($data);
            ksort($conditionfield);
            $updatedata = null;
            $wheredata = null;

            foreach ($data as $key => $value) {
                $updatedata .= $key . ' = :' . $key . ', ';
            }

            $updatedata = rtrim($updatedata, ', ');

            foreach ($conditionfield as $key => $value) {
                $wherekey = 'where' . $key;
                $conditionfield[$wherekey] = $conditionfield[$key];
                unset($conditionfield[$key]);
                $wheredata .= $key . ' = :' . $wherekey . ' AND ';
            }
            $wheredata = rtrim($wheredata, ' AND ');

            $finalarray = array_merge($data, $conditionfield);
            ksort($finalarray);

            $query = 'UPDATE ' . $table . ' SET ' . $updatedata . ' WHERE ' . $wheredata;
            $statement = $this->PDO->prepare($query);

            foreach ($finalarray as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            $statement->execute();

            return $statement->rowCount();

            $statement = null;
            $this->PDO = null;
        } catch (Exception $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    /**
     * 
     * @param String $query query to database
     * @param Array $conditionfield key => field database, value => where value data
     * @param PDO $fetchmode FETCH MODE database
     * @return Mixed Integer = row affected, String HTML error 
     */
    function _select($query, $conditionfield = array(), $fetchmode = PDO::FETCH_ASSOC) {
        try {
            $statement = $this->PDO->prepare($query);

            foreach ($conditionfield as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();

            $this->_rr = $statement->rowCount();
            return $this->_htmlspecial_array($statement->fetchall($fetchmode));

            $statement = null;
            $this->PDO = null;
        } catch (PDOException $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    function _customexec($query, $conditionfield = array()) {
        try {
            $statement = $this->PDO->prepare($query);
            foreach ($conditionfield as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();
            return $statement->rowCount();
            $statement = null;
            $this->PDO = null;
        } catch (PDOException $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    function _call($callfucntion, $fetchmode = PDO::FETCH_ASSOC) {
        try {
            $statement = $this->PDO->prepare($callfucntion);
            $statement->execute();

            $this->_rr = $statement->rowCount();
            return $this->_htmlspecial_array($statement->fetchall($fetchmode));

            $statement = null;
            $this->PDO = null;
        } catch (PDOException $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    /**
     * 
     * @param Sting $table table name
     * @param Array $conditionfield key => field database, value => where value data
     * @return Mixed Integer = row affected, String HTML error 
     */
    function _delete($table, $conditionfield = array()) {
        try {
            ksort($conditionfield);

            $wheredata = null;
            foreach ($conditionfield as $key => $value) {
                $wheredata .= ' ' . $key . ' = :' . $key . ' and';
            }
            $wheredata = rtrim($wheredata, 'and');

            $query = 'DELETE FROM ' . $table . ' WHERE ' . $wheredata;
            $statement = $this->PDO->prepare($query);
            foreach ($conditionfield as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            $statement->execute();

            return $statement->rowCount();

            $statement = null;
            $this->PDO = null;
        } catch (Exception $e) {
            if (DB_DEBUG) {
                echo '<div style="font-weight:bold;color:red;font-size:12pt;border:1px solid black;padding:10px;display:inline-block;">' . $query . '<hr/>' . $e->getMessage() . '</div>';
            }
        }
    }

    /**
     * 
     * @param Array $variable Data to encryption
     * @return Array Data decryption
     */
    function _htmlspecial_array($variable) {
        
        return $variable;
    }
    
     function _htmlspecial_array22(&$variable) {
        foreach ($variable as $valuae => &$value) {
            if (!is_array($value)) {
                $value = htmlspecialchars($value);
            } else {
                $this->_htmlspecial_array($value);
            }
        }
        return $variable;
    }

}
