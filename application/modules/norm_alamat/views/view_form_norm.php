<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

  <!-- begin:: Content Head -->
  <div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
      <div class="kt-subheader__main">
        <h3 class="kt-subheader__title">
          <?= $this->template_view->nama('judul').' - '.$title; ?>
        </h3>
      </div>
    </div>
  </div>
  <!-- end:: Content Head -->

  <!-- begin:: Content -->
  <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <form class="kt-form" id="form_norm">
          <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
              <div class="form-group row">
                <label class="col-12 col-form-label">Total Data Alamat : <strong><?=$counter_data;?></strong></label>
                <label class="col-12 col-form-label">Total Data Alamat (Sudah Dinormalisasi) : <strong><?= $counter_norm;?></strong></label>
                <label class="col-12 col-form-label">Total Data Alamat (Belum Dinormalisasi) : <strong><?= $selisih;?></strong></label>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">Jumlah Data</label>
                <div class="col-12">
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="la la-calculator"></i></span></div>
                    <input type="text" class="form-control numberinput" value="" placeholder="Jumlah Data" id="jumlah" name="jumlah">
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">Data yang diproses</label>
                <div class="col-12">
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="la la-cubes"></i></span></div>
                    <select class="form-control" name="kategori" id="kategori">
                      <option value="0">Belum Dinormalisasi</option>
                      <option value="1">Sudah Dinormalisasi</option>
                      <option value="all">Semua</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="kt-portlet__foot">
          <div class="kt-form__actions">
            <div class="row">
              <div class="col-lg-3 col-xl-3">
              </div>
              <div class="col-lg-9 col-xl-9">
                <button id="btn_norm" type="button" class="btn btn-success" onclick="proses_normalisasi()">Proses Normalisasi</button>&nbsp;
                <button type="reset" class="btn btn-secondary">Cancel</button>
              </div>
            </div>
          </div>
        </div>
        </form>
      </div>
  </div>

</div>

