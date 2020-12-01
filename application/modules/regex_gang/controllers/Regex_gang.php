<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regex_gang extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_user');
		$this->load->model('m_regex_gang');
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
			'title' => 'Pengelolaan Regex Gang',
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
			'modal' => 'modal_regex_gang',
			'js'	=> 'regex_gang.js',
			'view'	=> 'view_regex_gang'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_data()
	{
		$list = $this->m_regex_gang->get_datatable();
		// echo $this->db->last_query();exit;
		
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $val) {
			// $no++;
			$row = array();
			// $row[] = $no;
			$row[] = $val->regex;
			$row[] = $val->urutan;
			$row[] = $val->keterangan;
			
			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_data(\''.$val->id.'\')">
							<i class="la la-pencil"></i> Edit Data
						</button>
						<button class="dropdown-item" onclick="delete_data(\''.$val->id.'\')">
							<i class="la la-trash"></i> Hapus Data
						</button>
			';	

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

	public function edit_data()
	{
		$id = $this->input->post('id');
		$oldData = $this->m_regex_gang->get_by_id($id);
		
		$data = array(
			'old_data'	=> $oldData
		);
		
		echo json_encode($data);
	}

	public function add_data()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		
		$regex = trim($this->input->post('regex'));
		$urutan = trim($this->input->post('urutan'));
		$keterangan = trim($this->input->post('keterangan'));
		
		$arr_valid = $this->rule_validasi();

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		//cek urutan
		$cek_urut = $this->m_regex_gang->get_by_condition(['urutan' => $urutan], true);
		if($cek_urut){
			$data['inputerror'][] = 'urutan';
			$data['error_string'][] = 'Urutan Sudah Ada';
			$data['status'] = FALSE;
			
			echo json_encode($data);
			return;
		}

		$this->db->trans_begin();

		$data = [
			'id' => $this->m_regex_gang->get_max_id(),
			'regex' => $regex,
			'urutan' => $urutan,
			'keterangan' => $keterangan
		];
		
		$insert = $this->m_regex_gang->save($data);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan data';
		}

		echo json_encode($retval);
	}

	public function update_data()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id = $this->input->post('id_regex');
		$regex = trim($this->input->post('regex'));
		$urutan = trim($this->input->post('urutan'));
		$keterangan = trim($this->input->post('keterangan'));
		
		$arr_valid = $this->rule_validasi();

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		
		$data = [
			'regex' => $regex,
			'urutan' => $urutan,
			'keterangan' => $keterangan
		];

		$where = ['id' => $id];
		$update = $this->m_regex_gang->update($where, $data);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Master Data';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Master Data';
		}
		
		echo json_encode($data);
	}

	public function delete_data()
	{
		$id = $this->input->post('id');
		$del = $this->m_regex_gang->delete_by_id($id);

		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Sukses dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Gagal dihapus';
		}

		echo json_encode($retval);
	}

	public function edit_status_user($id)
	{
		$input_status = $this->input->post('status');
		// jika aktif maka di set ke nonaktif / "0"
		$status = ($input_status == "aktif") ? $status = 0 : $status = 1;
			
		$input = array('status' => $status);

		$where = ['id' => $id];

		$this->m_user->update($where, $input);

		if ($this->db->affected_rows() == '1') {
			$data = array(
				'status' => TRUE,
				'pesan' => "Status User berhasil di ubah.",
			);
		}else{
			$data = array(
				'status' => FALSE
			);
		}

		echo json_encode($data);
	}

	// ===============================================
	private function rule_validasi()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('regex') == '') {
			$data['inputerror'][] = 'regex';
            $data['error_string'][] = 'Wajib Mengisi Regex';
            $data['status'] = FALSE;
		}

		if ($this->input->post('urutan') == '') {
			$data['inputerror'][] = 'urutan';
            $data['error_string'][] = 'Wajib Mengisi Urutan';
            $data['status'] = FALSE;
		}

		if ($this->input->post('keterangan') == '') {
			$data['inputerror'][] = 'keterangan';
            $data['error_string'][] = 'Wajib Mengisi Keterangan';
            $data['status'] = FALSE;
		}

        return $data;
	}

	private function konfigurasi_upload_img($nmfile)
	{ 
		//konfigurasi upload img display
		$config['upload_path'] = './files/img/user_img/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp';
		$config['overwrite'] = TRUE;
		$config['max_size'] = '4000';//in KB (4MB)
		$config['max_width']  = '0';//zero for no limit 
		$config['max_height']  = '0';//zero for no limit
		$config['file_name'] = $nmfile;
		//load library with custom object name alias
		$this->load->library('upload', $config, 'file_obj');
		$this->file_obj->initialize($config);
	}

	private function konfigurasi_image_resize($filename)
	{
		//konfigurasi image lib
	    $config['image_library'] = 'gd2';
	    $config['source_image'] = './files/img/user_img/'.$filename;
	    $config['create_thumb'] = FALSE;
	    $config['maintain_ratio'] = FALSE;
	    $config['new_image'] = './files/img/user_img/'.$filename;
	    $config['overwrite'] = TRUE;
	    $config['width'] = 450; //resize
	    $config['height'] = 500; //resize
	    $this->load->library('image_lib',$config); //load image library
	    $this->image_lib->initialize($config);
	    $this->image_lib->resize();
	}

	private function konfigurasi_image_thumb($filename, $gbr)
	{
		//konfigurasi image lib
	    $config2['image_library'] = 'gd2';
	    $config2['source_image'] = './files/img/user_img/'.$filename;
	    $config2['create_thumb'] = TRUE;
	 	$config2['thumb_marker'] = '_thumb';
	    $config2['maintain_ratio'] = FALSE;
	    $config2['new_image'] = './files/img/user_img/thumbs/'.$filename;
	    $config2['overwrite'] = TRUE;
	    $config2['quality'] = '60%';
	 	$config2['width'] = 45;
	 	$config2['height'] = 45;
	    $this->load->library('image_lib',$config2); //load image library
	    $this->image_lib->initialize($config2);
	    $this->image_lib->resize();
	    return $output_thumb = $gbr['raw_name'].'_thumb'.$gbr['file_ext'];	
	}

	private function seoUrl($string) {
	    //Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);
	    return $string;
	}
}
