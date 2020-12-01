<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_regex_gang extends CI_Model
{
	var $table = 'm_regex_gang';
	// var $column_search = ['m_alamat.alamat','m_alamat.no_rt','m_alamat.no_rw', 'm_alamat.kode_pos', 'kel."NAMA_KEL"','kec."NAMA_KEC"'];

	var $column_search = ['regex', 'urutan', 'keterangan'];
	
	var $column_order = [
		null, 
		'regex',
		'urutan',
		'keterangan',
		null
	];

	var $order = ['urutan' => 'asc']; 

	public function __construct()
	{
		parent::__construct();
		//alternative load library from config
		$this->load->database();
	}

	private function _get_datatables_query($term='')
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$i = 0;
		// loop column 
		foreach ($this->column_search as $item) 
		{
			// if datatable send POST for search
			if($_POST['search']['value']) 
			{
				// first loop
				if($i===0) 
				{
					// open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->group_start();
					// $this->db->like($item, $_POST['search']['value']);
					$this->db->where("$item ilike '%".$_POST['search']['value']."%'");
				}
				else
				{
					//$this->db->or_like($item, $_POST['search']['value']);
					$this->db->or_where("$item ilike '%".$_POST['search']['value']."%'");
				}
				//last loop
				if(count($this->column_search) - 1 == $i) 
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatable()
	{
		$term = $_REQUEST['search']['value'];
		$this->_get_datatables_query($term);
		if($_REQUEST['length'] != -1)
		$this->db->limit($_REQUEST['length'], $_REQUEST['start']);

		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	
	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_condition($where = false, $is_single = false)
	{
		$this->db->from($this->table);
		if($where) {
			$this->db->where($where);
		}

		$this->db->order_by('urutan', 'asc');
		
		$query = $this->db->get();
		if($is_single) {
			return $query->row();
		}else{
			return $query->result();
		}
	}

	public function save($data)
	{
		return $this->db->insert($this->table, $data);	
	}

	public function update($where, $data)
	{
		return $this->db->update($this->table, $data, $where);
	}

	public function softdelete_by_id($id)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$where = ['id' => $id];
		$data = ['deleted_at' => $timestamp, 'status' => null];
		return $this->db->update($this->table, $data, $where);
	}

	public function delete_by_id($id)
	{
		return $this->db->delete($this->table, ['id' => $id]); 
	}
	
	public function get_max_id()
	{
		$q = $this->db->query("SELECT MAX(id) as kode_max from $this->table");
		$kd = "";
		if($q->num_rows()>0){
			$kd = $q->row();
			return (int)$kd->kode_max + 1;
		}else{
			return '1';
		} 
	}
}