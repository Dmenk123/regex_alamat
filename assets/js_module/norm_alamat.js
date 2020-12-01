var save_method;
var table;

$(document).ready(function() {
    filter_data();
    
    //force integer input in textfield
    $('input.numberinput').bind('keypress', function (e) {
        return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) ? false : true;
    });

    $(".modal").on("hidden.bs.modal", function(){
        reset_modal_form();
        reset_modal_form_import();
    });
});	

function filter_data(){
    var kategori = $('#kategori').val();

    //datatables
	table = $('#tabel_data').DataTable({
        destroy: true,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        iDisplayLength: 25,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
		ajax: {
			url  : base_url + "norm_alamat/list_data",
            type : "POST",
            data : {kategori:kategori},
            dataType : 'JSON',
		},

		//set column definition initialisation properties
		columnDefs: [
			{
				targets: [-1], //last column
				orderable: false, //set not orderable
			},
		],
    });
}

function proses_normalisasi() {
    $("#btn_norm").prop('disabled', true);
    var data = {
        kategori : $('#kategori').val(),
        jumlah : $('#jumlah').val()
    };

    $('#pesan_modal').modal('show');
    //ambil data
    $.ajax({
        url:  base_url + 'norm_alamat/generate',
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(response){
            if (response.status == 'gagal') {
                $('#pesan_modal').modal('hide');
                swal.fire("Gagal!!", "Proses gagal", "danger");
                $('#btn_norm').prop('disabled', false);
            }else{
                var i = 0;
                var i_max = response.data.length;
                proses_normalisasi_perdata(i, i_max, response);
                console.log("maksimal = " + i_max, response);
            }
        }
    });
}

function proses_normalisasi_perdata(i, i_max, data_kirim) {
    //console.log(i);
    var persen = Math.round((i / i_max) * 100);
    $('.progress-bar').attr("aria-valuenow", persen).css("width", persen+'%').text(persen + '%');
    $('#proses-data').text('Proses ke : '+ i +' dari total '+ i_max +' data.');
    var data_alamat = {
        id_alamat 		: data_kirim.data[i].id,
        alamat 			: data_kirim.data[i].alamat,
        urut 			: i
    };
    //console.log(data_pegawai);
    $.ajax({
        url:  base_url + 'norm_alamat/proses_generate_per_alamat',
        type: 'POST',
        dataType: 'json',
        data: data_alamat,
        success: function(data){
            if (data.status == 'gagal') {
                $('#pesan_isi').html(isi);
            }
            else
            {
                i = i + 1;
                console.log(i);
                if(i < i_max)
                {
                    proses_normalisasi_perdata(i, i_max, data_kirim);
                }
                else
                {
                    swal.fire("Data Alamat Sukses di Normalisasi")
                        .then((value) => {
                            // swal.fire(`The returned value is: ${value}`);
                            window.location = base_url+"norm_alamat";
                    });
                }
            }
        }
    });
}

//////////////////////////////////////////
function add_menu()
{
    reset_modal_form();
    save_method = 'add';
	$('#modal_user_form').modal('show');
	$('#modal_title').text('Tambah User Baru'); 
}

function edit_data(id)
{
    reset_modal_form();
    save_method = 'update';
    $.ajax({
        url : base_url + 'norm_alamat/edit_data',
        type: "POST",
        dataType: "json",
        data : {id:id},
        success: function(data)
        {
            $('[name="id_alamat"]').val(data.old_data.id);
            $('[name="alamat"]').val(data.old_data.alamat).attr('disabled', true);
            $('[name="jalan"]').val(data.old_data.jalan);
            $('[name="gang"]').val(data.old_data.gang);
            $('[name="nomor"]').val(data.old_data.nomor);
            $('#modal_edit').modal('show');
	        $('#modal_title').text('Edit Data Alamat'); 

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save()
{
    var url;
    var txtAksi;

    if(save_method == 'add') {
        url = base_url + 'norm_alamat/add_data';
        txtAksi = 'Tambah';
    }else{
        url = base_url + 'norm_alamat/update_data';
        txtAksi = 'Edit';
    }
    
    var form = $('#form-edit-alamat')[0];
    var data = new FormData(form);
    
    $("#btnSave").prop("disabled", true);
    $('#btnSave').text('Menyimpan Data'); //change button text
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: url,
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false, 
        cache: false,
        timeout: 600000,
        success: function (data) {
            if(data.status) {
                swal.fire("Sukses!!", "Aksi "+txtAksi+" Berhasil", "success")
                .then((value) => {
                    $("#btnSave").prop("disabled", false);
                    $('#btnSave').text('Simpan');
                });
                reset_modal_form();
                $(".modal").modal('hide');
                reload_table();
            }else {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    if (data.inputerror[i] != 'pegawai') {
                        $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback'); //select span help-block class set text error string
                    }else{
                        //ikut style global
                        $('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]).addClass('invalid-feedback-select');
                    }
                }

                $("#btnSave").prop("disabled", false);
                $('#btnSave').text('Simpan');
            }
        },
        error: function (e) {
            console.log("ERROR : ", e);
            $("#btnSave").prop("disabled", false);
            $('#btnSave').text('Simpan');

            reset_modal_form();
            $(".modal").modal('hide');
        }
    });
}

function batalkan_normalisasi(id){
    swalConfirmDelete.fire({
        title: 'Batalkan Normalisasi ?',
        text: "Data akan diset belum dinormalisasi ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan !',
        cancelButtonText: 'Tidak!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
            $.ajax({
                url : base_url + 'norm_alamat/batalkan_normalisasi',
                type: "POST",
                dataType: "JSON",
                data : {id:id},
                success: function(data)
                {
                    swalConfirm.fire('Berhasil Hapus User!', data.pesan, 'success');
                    table.ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    Swal.fire('Terjadi Kesalahan');
                }
            });
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalConfirm.fire(
            'Dibatalkan',
            'Aksi Dibatalakan',
            'error'
          )
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}





function reset_modal_form()
{
    $('#form-edit-alamat')[0].reset();
    $('.append-opt').remove(); 
    $('div.form-group').children().removeClass("is-invalid invalid-feedback");
    $('[name="alamat"]').attr('disabled', false);
    $('span.help-block').text('');
}

function reset_modal_form_import()
{
    $('#form_import_excel')[0].reset();
    $('#label_file_excel').text('Pilih file excel yang akan diupload');
}

function import_excel(){
    $('#modal_import_excel').modal('show');
	$('#modal_import_title').text('Import data user'); 
}

function import_data_excel(){
    var form = $('#form_import_excel')[0];
    var data = new FormData(form);
    
    $("#btnSaveImport").prop("disabled", true);
    $('#btnSaveImport').text('Import Data');
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: base_url + 'master_user/import_data_master',
        data: data,
        dataType: "JSON",
        processData: false, // false, it prevent jQuery form transforming the data into a query string
        contentType: false, 
        success: function (data) {
            if(data.status) {
                swal.fire("Sukses!!", data.pesan, "success");
                $("#btnSaveImport").prop("disabled", false);
                $('#btnSaveImport').text('Simpan');
            }else {
                swal.fire("Gagal!!", data.pesan, "error");
                $("#btnSaveImport").prop("disabled", false);
                $('#btnSaveImport').text('Simpan');
            }

            reset_modal_form_import();
            $(".modal").modal('hide');
            table.ajax.reload();
        },
        error: function (e) {
            console.log("ERROR : ", e);
            $("#btnSaveImport").prop("disabled", false);
            $('#btnSaveImport').text('Simpan');

            reset_modal_form_import();
            $(".modal").modal('hide');
            table.ajax.reload();
        }
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#div_preview_foto').css("display","block");
        $('#preview_img').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    } else {
        $('#div_preview_foto').css("display","none");
        $('#preview_img').attr('src', '');
    }
}

