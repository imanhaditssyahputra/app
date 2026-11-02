<?= main_header('Inventory', 'Import Data barang') ?>

<div class="row">
    <div class="col-md-12">
        <div class="note note-success">
            <p>
                Modul ini berfungsi untuk import data Barang dari file CSV.<br>
                Urutan Kolom : <br>
                Kode Barang, Nama Barang, Harga Beli, Harga Jual, Stok, Satuan
            </p>
        </div>
        <!--h2>Import Data Pendapatan Pasar</h2-->
        <div class="panel panel-default">
            <div class="panel-heading bg-light" style="font-size:13px; padding:10px 15px;">
                Data Barang
                <!-- <a data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="float:right;" href="#" onclick="$('#importFrm').slideToggle();"></a> -->
                <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" style="float:right;">
                    Import Data
                </a>
            </div>

            <p>

            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form class="d-flex" enctype="multipart/form-data">
                        <input type="file" name="file" id="file">
                        <label style="font-size: 14px !important; padding:5px 10px;">Kategori</label>
                        <select id="kateg" name="kateg" class="form-control input-medium select2me" data-placeholder="Pilih...">
                            <option value="6">Non Elektromedik Steril</option>
                            <option value="7">Non Elektromedik Non Steril</option>
                            <option value="8">Elektromedik Non Radiasi</option>
                            <option value="9">Diagnostik In Vitro</option>
                            <option value="10">Non Alkes (Suket)</option>
                            <option value="11">New</option>
                        </select>
                        <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                    </form>

                </div>
            </div>
            <div class="panel-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center">#id</th>
                            <th style="text-align: center">Kode</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Harga Jual</th>
                            <th style="text-align: center">Stok</th>
                            <th style="text-align: center">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9">Tidak ada data.....</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>