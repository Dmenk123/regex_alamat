<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_alamat extends CI_Model
{
	var $table = 'm_alamat';
	////////////////////////////////////////////////////////
	var $column_search = ['m_alamat.alamat'];
	var $column_order = [
		null, 
		'm_alamat.alamat',
		'm_alamat.no_rt',
		'm_alamat.no_rw',
		'm_alamat.kode_pos',
		'nama_kec',
		'nama_kel',
		null
	];

	var $order = ['m_user.username' => 'desc'];
	
	/////////////////////////////////////////////////////////

	var $column_search2 = ['m_alamat.alamat'];
	var $column_order2 = [
		null, 
		'm_alamat.alamat',
		'm_alamat.no_rt',
		'm_alamat.no_rw',
		'm_alamat.kode_pos',
		'nama_kec',
		'nama_kel',
		null
	];

	var $order2 = ['m_user.username' => 'desc']; 
	public function __construct()
	{
		parent::__construct();
		//alternative load library from config
		$this->load->database();
	}

	private function _get_datatables_query($term='')
	{
		$this->db->select('m_alamat.*, kec."NAMA_KEC" as nama_kec, kel."NAMA_KEL" as nama_kel');
		$this->db->from($this->table);
		// $this->db->join('setup_kec_duk kec', 'm_alamat.no_kec = kec."NO_KEC"', 'left');
		// $this->db->join('setup_kel_duk kel', 'm_alamat.no_kel = kel."NO_KEL"', 'left');	
		$this->db->join('setup_kec_duk as kec', '(((kec."NO_KEC" = m_alamat.no_kec::numeric) AND (kec."NO_KAB" = 78) AND (kec."NO_PROP" = 35)))','left');
		
		$this->db->join('setup_kel_duk as kel','((kel."NO_KEL" = m_alamat.no_kel::numeric) AND (kel."NO_KEC" = m_alamat.no_kec::numeric) AND (kel."NO_KAB" = 78) AND (kel."NO_PROP" = 35))', 'left');
		//$this->db->where('kec."NO_KAB" = 78 and kec."NO_PROP" = 35 and kel."NO_KAB" = 78 and kel."NO_PROP" = 35');
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

	private function _get_datatables_norm_query($term='', $kategori)
	{
		$this->db->select('m_alamat.*');
		$this->db->from($this->table);

		if($kategori == '1') {
			$where = ['is_norm' => 1];
			$this->db->where($where);
		}elseif($kategori == '0'){
			$where = ['is_norm' => null];
			$this->db->where($where);
		}

		$i = 0;
		// loop column 
		foreach ($this->column_search2 as $item) 
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
				if(count($this->column_search2) - 1 == $i) 
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order2))
		{
			$order = $this->order2;
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

	public function count_norm_all(){
		$this->db->from($this->table);
		$this->db->where(['is_norm' => 1]);
		return $this->db->count_all_results();
	}


	///////////////////////////////////normalisasi area//////////////////////////////////////////////
	function get_datatable_norm($kategori)
	{
		$term = $_REQUEST['search']['value'];
		$this->_get_datatables_norm_query($term, $kategori);
		if($_REQUEST['length'] != -1)
		$this->db->limit($_REQUEST['length'], $_REQUEST['start']);

		$query = $this->db->get();
		return $query->result();
	}
	//////////////////////////////////end nromalisasi area/////////////////////////////////////////

	public function get_detail_user($id_user)
	{
		$this->db->select('*');
		$this->db->from('m_user');
		$this->db->where('id', $id_user);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }
	}
	
	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_condition($where, $is_single = false)
	{
		$this->db->from($this->table);
		$this->db->where($where);
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