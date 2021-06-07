<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends CI_Controller
{

public function __construct()
{

parent::__construct();
$this->load->model('Pegawaipns_model', 'model');
$this->load->model('Pegawaitkk_model', 'model_tkk');
$this->load->model('Satker_model', 'satker');
$this->load->model('Tugas_model', 'tugas');
}

public function index()
{

//load view
$data['judul_pns']	= 'Data Pegawai PNS/Polri';
$data['judul_tkk']	= 'Data Pegawai Tenaga Kerja Kontrak';
$data['judul']		= 'Data Pegawai';

$this->load->view('media_view', [

'konten' => $this->load->view('view_pegawai/pegawailist_view', $data, true)
]);
}

/*
--------------------------- kelola data pegawai pns -------------------------------------------

*/
//menampilkan data dengan ajax datatables
public function ajaxList()
{
$list = $this->model->getDatatables();
$data = array();
$no = $_POST['start'];
foreach ($list as $list) {

$no++;
$row = array();
$row[] = $no;
$row[] = $list->gelar_depan.' '.$list->nama.' '.$list->gelar_belakang;
$row[] = $list->nip;
$row[] = $list->tempat_lahir.', '.format_indo($list->tgl_lahir);
$row[] = $list->jkel;
$row[] = $list->nama_jabatan;
$row[] = $list->nama_pangkat;
$row[] = $list->nama_satker;
$row[] = '
<td>
<a href="'.base_url($this->uri->segment(1).'/editpns/').$list->uuid_pegawai.'" class="btn btn-info btn-xs" style="color:#fff;">ubah</a>
<a href="'.base_url($this->uri->segment(1).'/deletepns/').$list->uuid_pegawai.'" class="btn btn-danger btn-xs" onClick="return konfirmasi()" style="color:#fff;">hapus</a>
</td>
';

$data[] = $row;
}

$output = array(
"draw" => $_POST['draw'],
"recordsTotal" => $this->model->countAll(),
"recordsFiltered" => $this->model->countFiltered(),
"data" => $data,
);
//output to json format
echo json_encode($output);
}

//tambah data
public function addpns()
{

//membuat validasi khusus
$rules = [

[
'field' => 'nip',
'label' => 'NIP/NRP',
'rules' => 'required|is_unique[pegawai_pns.nip]'
]
];

//terapkan validasi
$this->form_validation->set_rules($rules);
$this->form_validation->set_rules($this->model->rules());

//jalankan validasi, jika tidak ada masalah maka akan simpan data
if ($this->form_validation->run()) {

$this->session->set_flashdata('berhasil', 'Data tersimpan!');

$this->model->saveData();

redirect(base_url($this->uri->segment(1).'/addpns'));
}

//load view
$data['judul']			= 'Tambah Data Pegawai PNS/Polri';
$data['satker']			= $this->satker->getAlls();

$this->load->view('media_view', [

'konten' => $this->load->view('view_pegawai/pegawaipnsadd_view', $data, true)
]);
}

//edit data
public function editpns($id = null)
{

//cek jika id belum diset
if(!isset($id)) redirect(base_url($this->uri->segment(1)));

//cek jika Kode satker sudah terdaftar
$cek = $this->model->getByID($id);

//cek jika kode satker tidak sama dengan yang dimasukan dan akan melakukan validasi ulang
if($cek->nip != $this->input->post('nip')){

//membuat validasi khusus
$rules = [

[
'field' => 'nip',
'label' => 'NIP/NRP',
'rules' => 'required|is_unique[pegawai_pns.nip]'
]
];

//terapkan validasi
$this->form_validation->set_rules($rules);
}

//terapkan validasi
$this->form_validation->set_rules($rules);


//terapkan validasi
$this->form_validation->set_rules($this->model->rules());

//jalankan validasi, jika tidak ada masalah maka akan simpan data
if ($this->form_validation->run()) {

$this->session->set_flashdata('berhasil', 'Data tersimpan!');

$this->model->editData($id);

redirect(base_url($this->uri->segment(1).'/editpns/'.$id));
}

//load view
$data['judul'] 			= 'Ubah Data Pegawai PNS/Polri';
$data['row'] 			= $this->model->getByID($id);
$data['satker']			= $this->satker->getAlls();

$this->load->view('media_view', [

'konten' => $this->load->view('view_pegawai/pegawaipnsedit_view', $data, true)
]);
}

//hapus data
public function deletepns($id = null)
{

//redirect url jika id belum di set
if(!isset($id)) redirect(base_url($this->uri->segment(1)));

//tampilkan pesan berhasil jika data dihapus
if($this->model->delete($id))
{

$this->session->set_flashdata('berhasil', 'Data terhapus!');

redirect(base_url($this->uri->segment(1)));
}
}

/*
--------------------------- kelola data pegawai tkk -------------------------------------------

*/
//menampilkan data dengan ajax datatables
public function ajaxListtkk()
{
$list = $this->model_tkk->getDatatables();
$data = array();
$no = $_POST['start'];
foreach ($list as $list) {

$no++;
$row = array();
$row[] = $no;
$row[] = $list->nama;
$row[] = $list->tempat_lahir.', '.format_indo($list->tgl_lahir);
$row[] = $list->alamat;
$row[] = format_indo($list->tgl_mulai);
$row[] = format_indo($list->tgl_selesai);
$row[] = $list->jkel;
$row[] = $list->pendidikan;
$row[] = $list->agama;
$row[] = $list->nama_tugas;
$row[] = $list->nama_satker;
$row[] = '
<td>
<a href="'.base_url($this->uri->segment(1).'/edittkk/').$list->uuid_pegawai.'" class="btn btn-info btn-xs" style="color:#fff;">ubah</a>
<a href="'.base_url($this->uri->segment(1).'/deletetkk/').$list->uuid_pegawai.'" class="btn btn-danger btn-xs" onClick="return konfirmasi()" style="color:#fff;">hapus</a>
</td>
';

$data[] = $row;
}

$output = array(
"draw" => $_POST['draw'],
"recordsTotal" => $this->model_tkk->countAll(),
"recordsFiltered" => $this->model_tkk->countFiltered(),
"data" => $data,
);
//output to json format
echo json_encode($output);
}

//tambah data
public function addtkk()
{


$this->form_validation->set_rules($this->model_tkk->rules());

//jalankan validasi, jika tidak ada masalah maka akan simpan data
if ($this->form_validation->run()) {

$this->session->set_flashdata('berhasil', 'Data tersimpan!');

$this->model_tkk->saveData();

redirect(base_url($this->uri->segment(1).'/addtkk'));
}

//load view
$data['judul']			= 'Tambah Data Pegawai Tenaga Kerja Kontrak';
$data['satker']			= $this->satker->getAlls();
$data['tugas']			= $this->tugas->getAlls();

$this->load->view('media_view', [

'konten' => $this->load->view('view_pegawai/pegawaitkkadd_view', $data, true)
]);
}

//edit data
public function edittkk($id = null)
{

//cek jika id belum diset
if(!isset($id)) redirect(base_url($this->uri->segment(1)));

//cek jika Kode ktp sudah terdaftar
$cek = $this->model_tkk->getByID($id);

//terapkan validasi
$this->form_validation->set_rules($this->model_tkk->rules());

//jalankan validasi, jika tidak ada masalah maka akan simpan data
if ($this->form_validation->run()) {

$this->session->set_flashdata('berhasil', 'Data tersimpan!');

$this->model_tkk->editData($id);

redirect(base_url($this->uri->segment(1).'/edittkk/'.$id));
}

//load view
$data['judul'] 			= 'Ubah Data Pegawai Tenaga Kerja Kontrak';
$data['row'] 			= $this->model_tkk->getByID($id);
$data['satker']			= $this->satker->getAlls();
$data['tugas']			= $this->tugas->getAlls();

$this->load->view('media_view', [

'konten' => $this->load->view('view_pegawai/pegawaitkkedit_view', $data, true)
]);
}

//hapus data
public function deletetkk($id = null)
{

//redirect url jika id belum di set
if(!isset($id)) redirect(base_url($this->uri->segment(1)))
//tampilkan pesan berhasil jika data dihapus
if($this->model_tkk->delete($id))
{

$this->session->set_flashdata('berhasil', 'Data terhapus!');

redirect(base_url($this->uri->segment(1)));
}
}
}
