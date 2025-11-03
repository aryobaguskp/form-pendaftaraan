<!DOCTYPE html>
<html>
<body>
<form action="save.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="namaLengkap" value="Test User" required><br>
    <input type="text" name="tempatLahir" value="Jakarta" required><br>
    <input type="date" name="tanggalLahir" value="2000-01-01" required><br>
    <input type="radio" name="jenisKelamin" value="Laki-laki" checked> Laki-laki<br>
    <select name="agama" required><option value="Islam">Islam</option></select><br>
    <textarea name="alamat" required>Jl. Test</textarea><br>
    <input type="tel" name="noTelepon" value="081234567890" required><br>
    <input type="email" name="email" value="test@email.com" required><br>
    <input type="text" name="nik" value="1234567890123456" required><br>
    <input type="file" name="pasFoto" required><br>
    <input type="text" name="asalSekolah" value="SMA Test" required><br>
    <input type="text" name="jurusanSekolah" value="IPA" required><br>
    <input type="number" name="tahunLulus" value="2020" required><br>
    <input type="text" name="noIjazah" value="123456" required><br>
    <input type="number" name="nilaiRata" value="85.5" required><br>
    <input type="text" name="namaAyah" value="Ayah Test" required><br>
    <input type="text" name="namaIbu" value="Ibu Test" required><br>
    <input type="text" name="pekerjaanAyah" value="Wiraswasta" required><br>
    <input type="text" name="pekerjaanIbu" value="IRT" required><br>
    <select name="pendidikanAyah" required><option value="SMA">SMA</option></select><br>
    <select name="pendidikanIbu" required><option value="SMA">SMA</option></select><br>
    <textarea name="alamatOrtu" required>Jl. Test Ortu</textarea><br>
    <input type="tel" name="noTeleponOrtu" value="081234567890" required><br>
    <select name="penghasilan" required><option value="1-3 juta">1-3 juta</option></select><br>
    <select name="prodi1" required><option value="Teknik Informatika">TI</option></select><br>
    <select name="jalurPendaftaran" required><option value="Reguler">Reguler</option></select><br>
    <button type="submit">SUBMIT TEST</button>
</form>
</body>
</html>