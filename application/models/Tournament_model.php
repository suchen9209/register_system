<?php 
class Tournament_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('tournament','id');
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
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

    public function add_one_applicant($id){
        $this->db->where('id',$id);
        $this->db->set('now_num', 'now_num + 1', false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();
    }
    
}
?>