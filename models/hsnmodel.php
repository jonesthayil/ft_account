<?php

class HsnModel extends Model {
    
    
    public function getList() {
        $sql = "select * from hsn_codes where 1=1 order by id";
        $this->_setSql($sql);
        $user = $this->getAll();
        if (empty($user)){ return false; }
        return $user;
    }

    public function getNameList() {
        $sql = "select id, name from hsn_codes where 1=1 and status=1";
        $this->_setSql($sql);
        $user = $this->getAll();
        if (empty($user)){ return false; }
        return $user;
    }
    
    public function get($id) {
        $sql = "select * from hsn_codes where id = ? limit 1";
        $this->_setSql($sql);
        $user = $this->getRow(array($id));
        if (empty($user)){ return false; }
        return $user;
    }
    
    
    public function update($id, $updateRecord) {
        
        $fields = array_keys($updateRecord);
        
        $sql = "update hsn_codes set ";
        
        foreach ($fields as $field) { $sql .= " $field = ?,"; }
        $sql = substr($sql, 0, -1);
        $sql .= " where id = ?";
        
        $data = array_values($updateRecord);
        $data[] = $id;
        
        //echo '<pre>'; print_r($data);
        
        $sth = $this->_db->prepare($sql);
        
        return $sth->execute($data);
    }
    
    public function save($data) {
        
        $insert_values = array();
        $datafields = array_keys($data);
        $question_marks = array();
        
        $question_marks[] = '('  . $this->placeholders('?', sizeof($data)) . ')';
        $insert_values = array_merge($insert_values, array_values($data));
        
        $sql = "INSERT INTO hsn_codes (" . implode(",", $datafields ) . ") VALUES " .
            implode(',', $question_marks);
            $stmt = $this->_db->prepare ($sql);
            if($stmt->execute($insert_values)) { return $this->_db->lastInsertId(); }
            else { return false; }
    }
    
    public function getLastId() {
        $sql = "select id from hsn_codes order by id desc limit 1";
        $this->_setSql($sql);
        $user = $this->getrow();
        if (empty($user)){ return false; }
        return $user;
    }
    
    
    public function getCustomersByGroup($id) {
        $sql = "select * from hsn_codes where group_id = ? ";
        $this->_setSql($sql);
        $user = $this->getAll(array($id));
        if (empty($user)){ return false; }
        return $user;
    }
    
}