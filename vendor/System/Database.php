<?php

namespace System;

use PDO;
use PDOException;

/**
 * Description of Database
 *
 * @author Samy
 */
class Database {
    /**
     * Application object
     * @var \System\Application
     */
    private $app;
    
    /**
     * PDO connection
     * @var \PDO
     */
    private static $connection;
    
    /**
     * Table name
     * @var string
     */
    private $table;
    
    /**
     * Data container
     * @var array
     */
    private $data = [];
    
    /**
     * Bindings container
     * @var array
     */
    private $bindings = [];
    
    /**
     * Last inserted ID
     * @var int
     */
    private $lastId;
    
    /**
     * Wheres
     * @var array
     */
    private $wheres = [];
    
    /**
     * Selects
     * @var array
     */
    private $selects = [];
    
    /**
     * Joins
     * @var array
     */
    private $joins = [];
    
    /**
     * Limit
     * @var int
     */
    private $limit;
    
    /**
     * Offset
     * @var int
     */
    private $offset;
    
    /**
     *Order BY
     * @var array 
     */
    private $orderBy = [];
    
    /**
     * Total rows
     * @var int
     */
    private $rows = 0;
    
    /*
     * Constructor
     * @param \System\Application $app
     * @return object
     */
    public function __construct(Application $app) {
        $this->app = $app;
        if (! $this->isConnected()){
            $this->connect();
        }
    }
    /**
     * Determine if there is any connection to the database
     * @return bool
     */
    private function isConnected() {
        return static::$connection instanceof PDO; //used to determine whether a PHP variable is an instantiated object of a certain class, returns bool.
    }
    /**
     * Connect to the database
     * @return void
     */
    private function connect() {
        $connectionData = $this->app->file->call('config.php');
        try{
            static::$connection = new PDO('mysql:host=' . $connectionData['server'] . ';' . 'dbname=' . $connectionData['dbname'], $connectionData['dbuser'], $connectionData['dbpass']);
            static::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            static::$connection->exec('SET NAMES utf8');
            
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    /**
     * Get database connection PDO object
     * @return \PDO
     */
    public function connection(){
        return static::$connection;
    }
    /**
     * Set Select clause
     * @param string $select
     * @return $this
     */
    public function select($select) {
        $this->selects[] = $select;
        return $this;
    }
    /**
     * Set Join clause
     * @param string $join
     * @return $this
     */
    public function join($join) {
        $this->joins[] = $join;
        return $this;
    }
    /**
     * Set Limit and offset
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = 0) {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    /**
     * Set Order by clause
     * @param string $column
     * @param string $sort
     * @return $this
     */
    public function orderBy($column, $sort = 'ASC') {
        $this->orderBy = [$column, $sort];
        return $this;
    }
    /**
     * Fetch table
     * This will return only one record
     * @param string $table
     * @return \stdClass | null  //stdClass as the fetch type is set to object
     */
    public function fetch($table = null) {
        if ($table){
            $this->table($table); 
        }
        $sql = $this->fetchStatement();
        $result = $this->query($sql, $this->bindings)->fetch();
        $this->reset();
        return $result;
        
    }
    /**
     * Fetch all records from table
     * @param string $table
     * @return array
     */
    public function fetchAll($table = null) {
        if ($table){
            $this->table($table); 
        }
        $sql = $this->fetchStatement();
        $query = $this->query($sql, $this->bindings);
        $results = $query->fetchAll();
        $this->rows = $query->rowCount();
        $this->reset();
        return $results;
    }
    /**
     * Get total rows from last fetch all statement
     * @return int
     */
    public function rows() {
        return $this->rows;
    }
    /**
     * prepare select (fetch) statement
     * @return string
     */
    private function fetchStatement() {
        $sql = 'SELECT ';
        if ($this->selects){
            $sql .= implode(',', $this->selects); // as it will be written in the controller like $this->db->select('name','age')
        }else{
            $sql .= '*';
        }
        $sql .= ' FROM ' . $this->table . ' ';
        if ($this->joins){
            $sql .= implode(' ', $this->joins);
        }
        if ($this->wheres){
            $sql .= ' WHERE ' . implode(' ', $this->wheres) . ' ';
        }
        if ($this->limit){
            $sql .= ' LIMIT ' . $this->limit;
        }
        if ($this->offset){
            $sql .= ' OFFSET ' . $this->offset;
        }
        if ($this->orderBy){
            $sql .= ' ORDER BY ' . implode(' ', $this->orderBy);
        }
        return $sql;
    }
    /**
     * Set the table name
     * @param string $table 
     * @return $this
     */
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    /**
     * Set the table name (Alias for the above method)
     * @param string $table 
     * @return $this
     */
    public function from($table) {
        return $this->table($table);
    }
    /**
     * Delete clause
     * @param string $table
     * @return $this
     */
    public function delete($table = null) {
        if ($table){
            $this->table($table);
        }
        $sql = 'DELETE FROM ' . $this->table . ' ';
        if ($this->wheres){
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
        }
        $this->query($sql, $this->bindings);
        $this->reset();
        return $this;
    }
    /**
     * Set the data that will be stored in a database table.
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    
    public function data($key, $value = null) {
        if (is_array($key)){
            $this->data = array_merge($this->data, $key);
            $this->addToBindings($key);
        } else {
            $this->data[$key] = $value;   
            $this->addToBindings($value);
        }
        return $this;
    }
    /**
     * Insert data into the database
     * @param string $table
     * @return $this
     */
    public function insert($table = null) {
        if ($table){
            $this->table($table);
        }
        $sql = 'INSERT INTO ' . $this->table . ' SET ';
        $sql .= $this->setFields();
        $this->query($sql, $this->bindings);
        $this->lastId = $this->connection()->lastInsertId();
        $this->reset();
        return $this;
    }
    /**
     * Update data in the database
     * @param string $table
     * @return $this
     */
    public function update($table = null) {
        if ($table){
            $this->table($table);
        }
        $sql = 'UPDATE ' . $this->table . ' SET ';
        $sql .= $this->setFields();
        if ($this->wheres){
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
        }
        $this->query($sql, $this->bindings);
        $this->reset();
        return $this;
    }
    /**
     * Set the fields for insert and update
     * @return string
     */
    private function setFields() {
        $sql = '';
        foreach (array_keys($this->data) as $key ){
            $sql .= '`' . $key . '` = ? , ';
            // $sql = INSERT INTO table_name SET `key` = ? , `key2` = ? , 
        }
        $sql = rtrim($sql, ', ');
        // $sql = INSERT INTO table_name SET `key` = ? , `key2` = ?
        return $sql;
    }
    /**
     * Add new where clause
     * @return $this
     */
    public function where() {
        $bindings = func_get_args();
        //pre($bindings);die;
        $sql = array_shift($bindings);
        $this->addToBindings($bindings);
        $this->wheres[] = $sql;
        return $this;
    }
    /**
     * Execute the given sql statement
     * @return \PDOStatement
     */
    public function query() {
        $bindings = func_get_args();
        $sql = array_shift($bindings); // get the first element value then remove it from the array
        if (count($bindings) == 1 && is_array($bindings[0])){
            $bindings = $bindings[0];
        }
        try{
            $query = $this->connection()->prepare($sql);
            foreach ($bindings as $key => $value) {
                $query->bindValue($key +1 , _e($value));
            }
            $query->execute();
            return $query;            
        } catch (PDOException $ex) {
            echo $sql;
            pre($this->bindings);
            die($ex->getMessage());
        }
    }
    /**
     * Get the last inserted ID
     * @return int
     */
    public function lastId() {
        return $this->lastId;
    }
    /**
     * Add the given value to bindings
     * @param mixed $value
     * @return void
     */
    private function addToBindings($value){
        if (is_array($value)){
            $this->bindings = array_merge($this->bindings, array_values($value));
        }else{
            $this->bindings[] = $value;            
        }
    }
    /**
     * Reset all data
     * @return void
     */
    private function reset() {
        $this->bindings = [];
        $this->data = [];
        $this->joins = [];
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = [];
        $this->selects = [];
        $this->table = null;
        $this->wheres = [];
    }
}
