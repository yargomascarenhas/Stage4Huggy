<?php

namespace Lib;

/**
*
*/
class PDOFilter {
    private $where = [];
    private $param = [];
    private $order;
    private $group;
    private $limit;

    function __construct() {
        # code...
    }

    public function addFilter($clause, array $param = array()) {
        $this->where[] = $clause;
        $this->param[] = $param;
    }

    public function setOrder($fields) {
        if (count($fields) > 0) {
            $this->order = ' ORDER BY ';
            foreach ($fields as $field => $direction) {
                $this->order .= $field . ' ' . $direction . ',';
            }
            $this->order = substr($this->order, 0, -1);
        }
    }

    public function setGroupBy($fields) {
        if (count($fields) > 0) {
            $this->group = ' GROUP BY ';
            foreach ($fields as $field) {
                $this->group .= $field . ',';
            }
            $this->group = substr($this->group, 0, -1);
        }
    }

    public function setLimit($start, $end) {
        $this->limit = ' LIMIT ' . $start . ',' . $end . ' ';
    }

    public function getWhere() {
        $strWhere = '';
        $strWhere = ' WHERE 1 ';

        foreach ($this->where as $condition) {
            $strWhere .= ' ' . $condition;
        }
        $strWhere .= ' ' . $this->group;
        $strWhere .= ' ' . $this->order;
        $strWhere .= ' ' . $this->limit;

        return $strWhere;
    }

    public function getParam() {
        $param = [];
        if (count($this->param) > 0) {
            foreach ($this->param as $value) {
                $param = array_merge($param, $value);
            }
        }
        return $param;
    }

    public function formatInField($field, $values){
        $arrayValues = explode(',', $values);
        foreach ($arrayValues as $key => $value) {
            $in[] = ':' . $field . $key;
            $valuesRet[':' . $field . $key] = $value;
        }
        return ['in' => implode(',', $in), 'values' => $valuesRet];
    }


    public function formatFullText($string) {
        $formated = '';
        $string = str_replace('.', '', $string);
        $array = explode(' ', $string);
        foreach ($array as $termo) {
            $termo = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $termo);
            $termo = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $termo);
            $termo = trim($termo);
            if($termo != 'de' && $termo != 'i' && !empty($termo)){
                $formated .= '+' . $termo . '* ';
            }
        }
        return trim($formated);
    }

}
