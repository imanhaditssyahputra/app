    <?= main_header('Saldo Customer', 'Saldo Awal Customer') ?>
    <?= main_periode('Daftar Saldo Awal Customer', 0) ?>
    <?= main_button($tambah, 1, 3, '', 'tambahDataModal', '') ?>

    <div class="row">
        <div class="col-12">
            <div style="overflow-x: auto;">
                <div class="datatable-example">

                    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Kode Customer</th>
                                <th style="text-align: center;">Nama Customer</th>
                                <th style="text-align: center;">Saldo</th>
                                <th style="text-align: center;">pembayaran</th>
                                <th style="text-align: center;">pengeluaran</th>
                                <th style="text-align: center;">&nbsp</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap modal -->
    
    <!-- /.modal -->


    <script>
        $(document).ready(function() {
            var bln = "<?= $bulan; ?>";
            var thn = "<?= $tahun; ?>";
            table = $('#table').DataTable({
                "processing": true, //Feature control the processing indicator.
                "ajax": {
                    "url": "<?= base_url('penjualan/datacustomer/saldo-table/') ?>" + bln + "/" + thn,
                    "type": "GET"
                },
                "aLengthMenu": [
                    [10, 15, 20, -1],
                    [10, 15, 20, "Semua"] // change per page values here
                ],
                // "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                //     var iTotal = 0;
                //     var iTotal1 = 0;
                //     for (var i = 0; i < aaData.length; i++) {
                //         iTotal += aaData[i][2] * 1;
                //         iTotal1 += aaData[i][3] * 1;
                //     }


                //     var iTot = 0;
                //     var iTot1 = 0;
                //     for (var i = iStart; i < iEnd; i++) {

                //         var x = aaData[aiDisplay[i]][2];
                //         var y = Number(x.replace(/[^0-9\.]+/g, ""));
                //         iTot += y;

                //         var x1 = aaData[aiDisplay[i]][3];
                //         var y1 = Number(x1.replace(/[^0-9\.]+/g, ""));
                //         iTot1 += y1;
                //     }

                //     var nCells = nRow.getElementsByTagName('td');
                //     nCells[1].innerHTML = number_format(iTot);
                //     nCells[2].innerHTML = number_format(iTot1);
                // }
            });
            // $('.add-btn').click(function() {
            //     $("#submit_form")[0].reset();
            // })

            // $("#submit_form").submit(function(e) {
            //     e.preventDefault();
            //     $id = $('#tableId').val();
            //     $.ajax({
            //         type: "post",
            //         url: "<?= base_url('bukubesar/datamaster/saldoawalakun-add') ?>",
            //         data: $(this).serialize(),
            //         dataType: "json",
            //         success: function(res) {
            //             if (res.error) {
            //                 if (res.oneErr) {
            //                     $("#kodeErr").html(res.oneErr)
            //                 } else {
            //                     $("#kodeErr").html('')
            //                 }
            //                 if (res.twoErr) {
            //                     $("#debetErr").html(res.twoErr)
            //                 } else {
            //                     $("#debetErr").html('')
            //                 }
            //                 if (res.threeErr) {
            //                     $("#krediErr").html(res.threeErr)
            //                 } else {
            //                     $("#krediErr").html('')
            //                 }

            //             } else {
            //                 swal({
            //                     title: "SALDO AWAL AKUN",
            //                     html: res.message,
            //                     type: "info",
            //                     confirmButtonText: "OK"
            //                 });
            //                 table_reload('table');
            //                 $('#tambahDataModal').modal('hide');
            //             }
            //         }
            //     })
            // })
            SelectInModalTahunBulan();
        });

        // function SaldoAwalEdit(id) {
        //     $('#tambahDataModal').modal('show');
        //     $.ajax({
        //         type: "post",
        //         url: "<?= base_url('bukubesar/datamaster/saldoawal-edit/') ?>" + id,
        //         dataType: "json",
        //         success: function(res) {
        //             $('select[name="kodeakun"]').val(res.kodeakun);
        //             $('[name="debet"]').val(res.debet);
        //             $('[name="kredit"]').val(res.kredit);
        //             $('[name="id"]').val(res.id);
        //         }
        //     });
        // }


        // function SaldoAwalDelete(id) {
        //     if (confirm('Are you sure you want to delete data ' + id + ' ?')) {
        //         $.ajax({
        //             type: "post",
        //             url: "<?= base_url('bukubesar/datamaster/saldoawal-delete/') ?>" + id,
        //             dataType: "json",
        //             success: function(res) {
        //                 swal('SALDO AWAL AKUN', 'Data berhasil di hapus', '');
        //                 table_reload('table');
        //             }
        //         });
        //     }
        // }

        function filterdata() {
            var bln = document.getElementById("periodebln").value;
            var thn = document.getElementById("periodethn").value;
            table.ajax.url("<?= base_url('bukubesar/datamaster/saldo-table/') ?>" + bln + "/" + thn).load();
        }
    </script>