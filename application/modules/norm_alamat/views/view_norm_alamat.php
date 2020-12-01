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
      <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
        </div>
        <div class="kt-portlet__head-toolbar">
          <div class="kt-portlet__head-wrapper">
            <div class="kt-portlet__head-actions row">
              <!-- <div><?= $this->template_view->getAddButton(false, 'add_menu'); ?></div> -->
              <div><a href="<?=base_url('norm_alamat/form_normalisasi')?>" class='btn btn-bold btn-label-brand btn-sm'><i class='la la-gear'></i>Normalisasi</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class="kt-portlet__body">
        <div class="row" style="padding-bottom: 20px;">
          <div class="col-md-3 row">
            <label class="col-form-label col-3">Kategori</label>
            <div class="col-lg-8">
              <select class="form-control" name="kategori" id="kategori">
                <option value="all">Semua</option>
                <option value="0">Belum Dinormalisasi</option>
                <option value="1">Sudah Dinormalisasi</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 row">
            <div>
              <button type="button" class="btn btn-brand" onclick="filter_data()">Cari</button>
            </div>
          </div>
        </div>
        <!-- <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div> -->
        <!--begin: Datatable -->
        <table class="table table-striped- table-bordered table-hover table-checkable" id="tabel_data">
          <thead>
            <tr>
              <th style="width: 5%;">No</th>
              <th>Alamat Master</th>
              <th>Jalan</th>
              <th>Gang/Blok</th>
              <th>Nomor</th>
              <th style="width: 5%;">Aksi</th>
            </tr>
          </thead>
        </table>

        <!--end: Datatable -->
      </div>
    </div>
  </div>
  
</div>



