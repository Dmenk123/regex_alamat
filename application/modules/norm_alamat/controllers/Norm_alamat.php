<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Norm_alamat extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_user');
		$this->load->model('m_regex_gang');
		$this->load->model('m_regex_nomor');
		$this->load->model('m_alamat');
		$this->load->model('m_global');
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = null;
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaan Data Alamat',
			'data_user' => $data_user,
			'data_role'	=> $data_role
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_form_norm',
			'js'	=> 'norm_alamat.js',
			'view'	=> 'view_norm_alamat'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_data()
	{
		$kategori = $this->input->post('kategori');
		$list = $this->m_alamat->get_datatable_norm($kategori);
		
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $val) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			$row[] = $val->alamat;
			$row[] = $val->jalan;
			$row[] = $val->gang;
			$row[] = $val->nomor;
			
			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_data(\''.$val->id.'\')">
							<i class="la la-pencil"></i> Edit Data
						</button>';	
			if($val->is_norm) {
				$str_aksi .= '<button class="dropdown-item" onclick="batalkan_normalisasi(\''.$val->id.'\')">
							<i class="la la-trash"></i> Batalkan Normalisasi
						</button>';
			}
			
			$str_aksi .= '</div></div>';
			$row[] = $str_aksi;
			$data[] = $row;
		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			//"recordsTotal" => $this->m_alamat->count_all(),
			//"recordsFiltered" => $this->m_alamat->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function form_normalisasi()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$counter_data = $this->m_alamat->count_all();
		$counter_norm = $this->m_alamat->count_norm_all();
		$selisih = (int)$counter_data - (int)$counter_norm;
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Form Normalisasi',
			'data_user' => $data_user,
			'counter_data'	=> $counter_data,
			'counter_norm' => $counter_norm,
			'selisih' => $selisih
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_form_norm',
			'js'	=> 'norm_alamat.js',
			'view'	=> 'view_form_norm'
		];

		$this->template_view->load_view($content, $data);
	}

	public function generate()
	{
		$kategori = $this->input->post('kategori');
		$limit = $this->input->post('jumlah');

		if($kategori == 'all') {
			$where = null;
		}elseif($kategori == '1'){
			$where = ['is_norm' => 1];
		}else{
			$where = ['is_norm' => null];
		}
		 
		//$data_alamat = $this->m_global->multi_row('id,alamat', 'is_norm is null and alamat ilike \'%bratang%\'', 'm_alamat', NULL, 'alamat asc', $limit);
		$data_alamat = $this->m_global->multi_row('id,alamat', $where, 'm_alamat', NULL, 'alamat asc', $limit);
		if($data_alamat) {
			$retval = [
				'status' => 'sukses',
				'data' => $data_alamat
			];
		}else{
			$retval = [
				'status' => 'gagal',
				'data' => null
			];
		}
		echo json_encode($retval);
	}

	public function proses_generate_per_alamat()
	{
		$id_alamat = $this->input->post('id_alamat');
		$alamat = $this->input->post('alamat');
		$urut = $this->input->post('urut');

		## isi data_gang [0] => jalan
		## isi data_gang [1] => gang (gang harus dipecah menjadi gang dan nomor)
		$data_gang = $this->split_gang($id_alamat, $alamat);
		
		## isi data_nomor [data_arr][0] => gang
		## isi data_nomor [data_arr][1] => nomor (tidak selalu ada pada data, ada yg index no 1 nya kosong yg berarti alamat tsb tidak ada gang)
		## isi data_nomor [is_gang] => true/false (jika true berarti ada gang, jika false cek jumlah data_arr. jika jumlah nya 2 index 0 adalah nama jalan, index 1 adalah nomor... jika jumlahnya 1  index no 0 adalah nomor)	
		$data_nomor = $this->pecah_nomor($data_gang);
		
		$this->db->trans_begin();

		foreach ($data_nomor as $key => $row) {
			// var_dump($row);exit;
			if(count($row['data_arr']) > 1) {
				if($row['is_gang']) {
					$data_upd = [
						'jalan' => $data_gang[$key]['arr_alamat'][0],
						'gang' => $row['data_arr'][0],
						'nomor' => $row['data_arr'][1],
						'is_norm' => 1
					];
					
				}else{
					$data_upd = [
						'jalan' => $row['data_arr'][0],
						'nomor' => $row['data_arr'][1],
						'is_norm' => 1
					];
				}
			}else{
				$data_upd = [
					'jalan' => $data_gang[$key]['arr_alamat'][0],
					'nomor' => $row['data_arr'][0],
					'is_norm' => 1
				];
			}

			// $retval[] = $data_upd;
			// $id_alamat = $row['id_alamat'];
			$update = $this->m_alamat->update(['id' => $id_alamat], $data_upd);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$response = ['status' => 'gagal'];
		}
		else {
			$this->db->trans_commit();
			$response = ['status' => 'sukses'];
		}

		echo json_encode($response);

	}

	public function split_gang($id_alamat, $alamat)
	{
		$regex_gang = $this->m_regex_gang->get_by_condition();
		$alamat = trim($alamat);
		foreach ($regex_gang as $keys => $gang) {
			$pattern = "/$gang->regex/";
			$arr_split = preg_split($pattern, $alamat, 2);
			if(count($arr_split) > 1) {
				$data['arr_alamat'] = $arr_split;
				$data['id_alamat'] = $id_alamat;
				$retval[] = $data;
				break;
			}else{
				if((count($regex_gang)-1) == $keys){
					$data['arr_alamat'] = $arr_split;
					$data['id_alamat'] = $id_alamat;
					$retval[] = $data;
					break;
				}else{
					continue;
				}
			}				
		}

		return $retval;
	}

	public function split_nomor($data_gang)
	{
		$regex_nomor = $this->m_regex_nomor->get_by_condition();
		
		foreach ($data_gang as $key => $row) {
			$is_gang = true;
			$id_alamat = $row['id_alamat'];
			// $id_alamat = 99;
			if(count($row['arr_alamat']) > 1) {
				$gang_raw = trim($row['arr_alamat'][1]);
				$is_gang = true;
			}else{
				$gang_raw = trim($row['arr_alamat'][0]);
				$is_gang = false;
			}
			
			foreach ($regex_nomor as $keys => $nmr) {
				$pattern = "/$nmr->regex/";
				$arr_split = preg_split($pattern, $gang_raw, 2);
				if(count($arr_split) > 1) {
					$data['data_arr'] = $arr_split;
					$data['is_gang'] = $is_gang;
					$data['id_alamat'] = $id_alamat;
					$retval[] = $data;
					break;
				}else{
					if((count($regex_nomor)-1) == $keys){
						$data['data_arr'] = $arr_split;
						$data['is_gang'] = $is_gang;
						$data['id_alamat'] = $id_alamat;
						$retval[] = $data;
						break;
					}else{
						continue;
					}
				}				
			}
		}

		return $retval;
	}

	############################# gae nyoba ##########################
	public function cek_rumus()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$limit = 5000;
		//$data_alamat = $this->m_global->multi_row('id,alamat', 'is_norm is null and alamat ilike \'%bratang%\'', 'm_alamat', NULL, 'alamat asc', $limit);
		$data_alamat = $this->m_global->multi_row('id,alamat', 'is_norm is null', 'm_alamat', NULL, 'alamat asc', $limit);
		// echo "<pre>";
		// print_r ($data_alamat);
		// echo "</pre>";
		// exit;
		## isi data_gang [0] => jalan
		## isi data_gang [1] => gang (gang harus dipecah menjadi gang dan nomor)
		$data_gang = $this->pecah_gang($data_alamat);
		// echo "<pre>";
		// print_r ($data_gang);
		// echo "</pre>";
		// exit;

		## isi data_nomor [data_arr][0] => gang
		## isi data_nomor [data_arr][1] => nomor (tidak selalu ada pada data, ada yg index no 1 nya kosong yg berarti alamat tsb tidak ada gang)
		## isi data_nomor [is_gang] => true/false (jika true berarti ada gang, jika false cek jumlah data_arr. jika jumlah nya 2 index 0 adalah nama jalan, index 1 adalah nomor... jika jumlahnya 1  index no 0 adalah nomor)	
		$data_nomor = $this->pecah_nomor($data_gang);
		// echo "<pre>";
		// print_r ($data_nomor);
		// echo "</pre>";


		foreach ($data_nomor as $key => $row) {
			// var_dump($row);exit;
			if(count($row['data_arr']) > 1) {
				if($row['is_gang']) {
					$data_upd = [
						'id_alamat' => $row['id_alamat'],
						'jalan' => $data_gang[$key]['arr_alamat'][0],
						'gang' => $row['data_arr'][0],
						'nomor' => $row['data_arr'][1],
						'is_norm' => 1
					];
					
				}else{
					$data_upd = [
						'id_alamat' => $row['id_alamat'],
						'jalan' => $row['data_arr'][0],
						'nomor' => $row['data_arr'][1],
						'is_norm' => 1
					];
				}
			}else{
				$data_upd = [
					'id_alamat' => $row['id_alamat'],
					'jalan' => $data_gang[$key]['arr_alamat'][0],
					'nomor' => $row['data_arr'][0],
					'is_norm' => 1
				];
			}

			$retval[] = $data_upd;
		}

		echo "<pre>";
		print_r ($retval);
		echo "</pre>";

		
	}

	public function pecah_gang($data_alamat)
	{
		$regex_gang = $this->m_regex_gang->get_by_condition();
		// var_dump($regex_gang);exit;
		foreach ($data_alamat as $key => $row) {
			$alamat = trim($row->alamat);
			$id_alamat = $row->id;
			
			foreach ($regex_gang as $keys => $gang) {
				$pattern = "/$gang->regex/";
				$arr_split = preg_split($pattern, $alamat, 2);
				// var_dump($arr_split);exit;
				//if (is_array($arr_split)){
				if(count($arr_split) > 1) {
					$data['arr_alamat'] = $arr_split;
					$data['id_alamat'] = $id_alamat;
					$retval[] = $data;
					break;
				}else{
					if((count($regex_gang)-1) == $keys){
						$data['arr_alamat'] = $arr_split;
						$data['id_alamat'] = $id_alamat;
						$retval[] = $data;
						break;
					}else{
						continue;
					}
				}				
			}
		}

		return $retval;
	}

	public function pecah_nomor($data_gang)
	{
		$regex_nomor = $this->m_regex_nomor->get_by_condition();
		
		foreach ($data_gang as $key => $row) {
			$is_gang = true;
			$id_alamat = $row['id_alamat'];
			// $id_alamat = 99;
			if(count($row['arr_alamat']) > 1) {
				$gang_raw = trim($row['arr_alamat'][1]);
				$is_gang = true;
			}else{
				$gang_raw = trim($row['arr_alamat'][0]);
				$is_gang = false;
			}
			
			foreach ($regex_nomor as $keys => $nmr) {
				$pattern = "/$nmr->regex/";
				$arr_split = preg_split($pattern, $gang_raw, 2);
				if(count($arr_split) > 1) {
					$data['data_arr'] = $arr_split;
					$data['is_gang'] = $is_gang;
					$data['id_alamat'] = $id_alamat;
					$retval[] = $data;
					break;
				}else{
					if((count($regex_nomor)-1) == $keys){
						$data['data_arr'] = $arr_split;
						$data['is_gang'] = $is_gang;
						$data['id_alamat'] = $id_alamat;
						$retval[] = $data;
						break;
					}else{
						continue;
					}
				}				
			}
		}

		return $retval;
	}
	############################# gae nyoba ##########################

	public function edit_data()
	{
		$id = $this->input->post('id');
		$oldData = $this->m_alamat->get_by_id($id);

		$data = array(
			'old_data'	=> $oldData
		);
		
		echo json_encode($data);
	}

	public function update_data()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id = $this->input->post('id_alamat');
		$jalan = trim($this->input->post('jalan'));
		$gang = trim($this->input->post('gang'));
		$nomor = trim($this->input->post('nomor'));
		
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		
		$arr_valid = $this->rule_validasi();

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();

		$data = [
			'jalan' => $jalan,
			'gang' => $gang,
			'nomor' => $nomor,
			'is_norm' => 1
		];

		$where = ['id' => $id];
		$update = $this->m_alamat->update($where, $data);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Master Alamat';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Master Alamat';
		}
		
		echo json_encode($data);
	}

	public function batalkan_normalisasi()
	{
		$id = $this->input->post('id');
		$data = [
			'jalan' => null,
			'gang' => null,
			'nomor' => null
		];

		$update = $this->m_alamat->update(['id' => $id], $data);
		if($update) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Alamat Sukses dibatalkan';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Alamat Gagal dibatalkan';
		}

		echo json_encode($retval);
	}

	private function rule_validasi()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('jalan') == '') {
			$data['inputerror'][] = 'jalan';
            $data['error_string'][] = 'Wajib Memilih Jalan';
            $data['status'] = FALSE;
		}

		// if ($this->input->post('gang') == '') {
		// 	$data['inputerror'][] = 'gang';
        //     $data['error_string'][] = 'Wajib Memilih Gang';
        //     $data['status'] = FALSE;
		// }

		if ($this->input->post('nomor') == '') {
			$data['inputerror'][] = 'nomor';
            $data['error_string'][] = 'Wajib Memilih Nomor';
            $data['status'] = FALSE;
		}

        return $data;
	}
	/////////////////////////////////////////////////////////////
}
