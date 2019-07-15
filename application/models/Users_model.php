<?php 
class Users_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('users','id');
    }

    public function get_info_by_username($username){
        $this->db->select('*');
        $this->db->where('username',$username);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('users.*,t.name');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->join('tournament as t','t.id = users.tid','LEFT');
        $this->db->from($this->table_name);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_num($parm=array()){
        $this->db->select('count(*) num');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num;
    }

    public function find($parm=array()){
        $this->db->select('*');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row() : false;
    }
    
}
?>