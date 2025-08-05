<?php include("include/head.php"); ?>



<section class="login-box" aria-labelledby="upload-section-title">
    <h5 id="upload-section-title">Check Picking</h5>
    <span>Excelファイル（.csv、.xls、.xlsx）<br>をアップロードしてください</span>
    <form action="upload_excel.php" method="post" enctype="multipart/form-data">
        <div class="input-group">
            <div class="input-group-prepend">Upload File</div>
            <div class="custom-file">
                <input type="file" id="excelFile" name="excel_file" accept=".csv,.xls,.xlsx" required
                    aria-describedby="fileLabel" />
                <label for="excelFile" id="fileLabel">Choose file</label>
            </div>
        </div>
        <div class="text-center" style="margin-bottom: 25px;">
            <button type="submit" class="btn btn-primary" aria-label="อัปโหลดไฟล์ Excel">📤 アップロード</button>
        </div>
    </form>
    <div class="text-center">
        <a href="scanpicking.php" class="btn btn-success" aria-label="เริ่มตรวจสอบบาร์โค้ด">
            <img src="https://img.icons8.com/color/96/barcode.png" alt="ไอคอนสแกนบาร์โค้ด"> チェック開始
        </a>
    </div>

</section>

</div>

<?php include("include/bottomnav.php"); ?>

<script>
    // อัพเดตชื่อไฟล์ที่เลือก
    const fileInput = document.getElementById('excelFile');
    const fileLabel = document.getElementById('fileLabel');

    fileInput.addEventListener('change', () => {
        const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'Choose file';
        fileLabel.textContent = fileName;
    });
</script>

</body>

</html>