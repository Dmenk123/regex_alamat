<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="modal_form">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form id="form-modal" name="form-modal">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id_regex" name="id_regex">
            <label for="" class="form-control-label">Sintak Regex:</label>
            <input type="text" class="form-control" id="regex" name="regex" autocomplete="off">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="" class="form-control-label">Urutan:</label>
            <input type="text" class="form-control numberinput" id="urutan" name="urutan" autocomplete="off">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="" class="form-control-label">Keterangan:</label>
            <textarea class="form-control" name="keterangan" id="keterangan" cols="30" rows="10"></textarea>
            <span class="help-block"></span>
		      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>
      </div>
    </div>
  </div>
</div>