<form id="formsantri" class="cmxform form-horizontal tasi-form" method="post" action="prospendaftaran.php?type=simpaninput">
    <div class="form-group">
        <label class="col-sm-2 control-label">Kode Pendaftaran</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" readonly="" required  name="kodedaftar" id="kodedaftar" value="<?php echo $kodedaftar ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Tanggal Daftar</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" autofocus="" placeholder="hh-bb-tttt" required  data-mask="99-99-9999" name="tanggaldaftar" id="tgldaftar" value="<?php echo $cekprosesdaftar['tanggal'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">NIK | Nama</label>
        <div class="col-sm-4">
            <input id="ktp" name="ktp"  placeholder="NIK" class="form-control" required="" type="text" value="" maxlength="16"/>
        </div>
        <div class="col-sm-6">
            <input id="nama" name="nama" autofocus="" placeholder="Nama" class="form-control" required="" type="text" value="" />
        </div>
    </div>    

    <div class="form-group">
        <label class="col-sm-2 control-label">Tempat Lahir</label>
        <div class="col-sm-4">
            <input id="tempatlahir" placeholder="Tempat Lahir" name="tempatlahir" class="form-control" required="" type="text" value="" />
        </div>
        <div class="col-sm-3">
            <input type="text" class="form-control" placeholder="hh-bb-tttt"  required name="tanggallahir" id="tanggallahir">

        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Jenis Kelamin</label>

        <div class="col-sm-2">
            <div class="radio">
                <label> <input  name="jeniskelamin" required="" type="radio" value="Laki-Laki"/>Laki-laki</label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label><input  name="jeniskelamin" required="" type="radio" value="Perempuan" />Perempuan</label>
            </div>
        </div>
        <div class="col-sm-3">
                <select id="goldarah"  name="goldarah" class="form-control" required="">
                    <option value="">Gol Darah:</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="O">O</option>
                    <option value="AB">AB</option>
                   
                </select>
            </div>
    </div>
        <div class="form-group">
        <label class="col-sm-2 control-label">Rencana Mukim</label>

        <div class="col-sm-2">
            <div class="radio">
                <label> <input  name="rencanamukim" required="" type="radio" value="Dalam"/>Dalam Pondok</label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="radio">
                <label><input  name="rencanamukim" required="" type="radio" value="Luar" />Luar Pondok</label>
            </div>
        </div>
        
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">NIK Wali | Nama</label>
        <div class="col-sm-4">
            <input id="ktp" name="ktpwali"  placeholder="NIK Wali" class="form-control" required="" type="text" value="" maxlength="16"/>
        </div>
        <div class="col-sm-6">
            <input id="nama" name="namawali" placeholder="Nama Wali" class="form-control" required="" type="text" value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Hubungan Wali</label>
        <div class="col-sm-5">
            <input placeholder="Hubungan Wali" name="hubunganwali" class="form-control" type="text"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Pekerjaan Wali</label>
        <div class="col-sm-5">
            <input placeholder="Pekerjaan" name="pekerjaan" class="form-control" type="text"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Nama Ayah |  Ibu</label>
        <div class="col-sm-5">
            <input id="nama" name="namaayah" placeholder="Nama Ayah" class="form-control" required="" type="text" value="" />
        </div>
        <div class="col-sm-5">
            <input id="nama" name="namaibu" placeholder="Nama Ibu" class="form-control" required="" type="text" value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Alamat</label>
        <div class="col-sm-4">
            <input placeholder="Alamat" name="alamat" class="form-control" type="text" /> 
        </div>
        <div class="col-sm-3">
            <input id="alamat" placeholder="Desa" name="desa" class="form-control" required="" type="text" value="" />
        </div>

    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-5">
            <select id="provinsi"  name="provinsi" class="form-control" required="">
                <option value="">Provinsi:</option>
                <?php
                foreach ($arrpropinsi as $kodeprov => $namaprov) {
                    echo "<option value='$kodeprov'>$namaprov</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-sm-5">
            <select id="kabupaten" name="kota" class="form-control" required="">
                <option value="">Kota/Kabupaten:</option>
            </select>

        </div>

    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>

        <div class="col-sm-3">
            <select id="kecamatan" name="kecamatan" class="form-control" required="">
                <option value="">Kecamatan</option>
            </select>
        </div><div class="col-sm-2">
            <input id="kodepos" placeholder="Kode Pos" name="kodepos" class="form-control" type="text" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">No HP 1</label>
        <div class="col-sm-3">
            <input id="hp" placeholder="No Hanphone 1" name="hp1" class="form-control" required="" type="text" />
        </div>
        <div class="col-sm-3">
            <input id="hp" placeholder="No Handphone 2" name="hp2" class="form-control" type="text" value="" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Email</label>
        <div class="col-sm-8">
            <input id="email" placeholder="Email" name="email" class="form-control"  type="email" value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Keterangan</label>
        <div class="col-sm-8">
            <textarea type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-6">
            <button type="submit" class="btn btn-danger">Simpan</button>
            <button type="reset" class="btn btn-default">Cancel</button>
        </div>

    </div>
</form>