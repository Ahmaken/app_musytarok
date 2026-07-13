<div class="col-lg-7">
    <section class="panel">
        <header class="panel-heading">
            Detail Setoran Simpanan 
            <hr>
            <table>
                <tr><td>Anggota </td><td>: <?php echo $kodeanggota ?></td></tr>
                <tr><td>Nama </td><td>: <?php echo $namaanggota ?></td></tr>
                <tr><td>Saldo </td><td>: <?php $qsaldo = mysql_fetch_array(mysql_query("select saldo from anggota_saldo where kode_anggota='$kodeanggota';"));
echo number_format($qsaldo['saldo'], 0, '.', '.'); ?></td></tr>
            </table>
        </header>
        <div class="panel-body">
            <div class="adv-table">

                <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th class="numeric">Debit</th>
                            <th class="numeric">Kredit</th>
                            <th class="numeric">Keterangan</th>
                            <th class="numeric">Jenis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //include '../../config/security.php';
                        $q = mysql_query("SELECT DATE_FORMAT(a.tanggal, '%d/%m/%Y') AS tanggal, a.kode, a.kode_anggota, b.nama, a.debet, a.kredit, a.jenis, a.keterangan FROM anggota_simpanan a, anggota_master b WHERE a.kode_anggota=b.kode AND kode_anggota='$kodeanggota' ORDER BY a.id ASC;");
                        $no = 1;
                        while ($r = mysql_fetch_array($q)) {
                            $kodetrans = urlencode(encryptIt($r['kode']));
                            echo "<tr><td>" . $no++ . "</td><td>" . $r['tanggal'] . "</td>"
                            . "<td class='numeric' id='harga'>" . number_format($r['debet'], 0, '.', '.') . "</td>"
                            . "<td class='numeric' id='harga'>" . number_format($r['kredit'], 0, '.', '.') . "</td>"
                            . "<td>" . $r['keterangan'] . "</td><td>" . $r['jenis'] . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</div>