<!-- progress bar modal -->
<div class="modal fade" id="pesan_modal" tabindex="-1" role="dialog" aria-labelledby="delete" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Proses Normalisasi Data, Mohon menunggu.</h4>
			</div>
			<div class="modal-body">
				<span id="proses-data"></span>
				<div class="progress">
				 	<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
				    0%
				  	</div>
				</div>
				<p id="pesan_isi"></p>
			</div>
		</div>
	</div>
</div>


<!-- Modal edit -->
<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="modal_edit">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form id="form-edit-alamat" name="form-edit-alamat">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id_alamat" name="id_alamat">
            <label for="" class="form-control-label">Alamat:</label>
            <input type="text" class="form-control" id="alamat" name="alamat" autocomplete="off" readonly>
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="" class="form-control-label">Jalan:</label>
            <input type="text" class="form-control" id="jalan" name="jalan" autocomplete="off">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="" class="form-control-label">Gang/Blok:</label>
            <input type="text" class="form-control" id="gang" name="gang" autocomplete="off">
            <span class="help-block"></span>
		  </div>
		  <div class="form-group">
            <label for="" class="form-control-label">Nomor:</label>
            <input type="text" class="form-control" id="nomor" name="nomor" autocomplete="off">
            <span class="help-block"></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>
      </div>
    </div>
  </div>
</div>
