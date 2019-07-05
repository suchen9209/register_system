<?php 
class Tournament_mail_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('tournament_mail','id');
    }

    public function get_mail($parm){
    	$this->db->select('*');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    
}
?>