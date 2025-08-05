<?php include("include/head.php"); ?>
<style>
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #121212;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #eee;
    }

    .item-card {
        background-color: #1e1e1e;
        color: #eee;
        padding: 20px 24px;
        margin-bottom: 18px;
        border-radius: 14px;
        border: 1px solid #444;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
        font-size: 15.5px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        width: 100%;
        max-width: 600px;
    }

    .item-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.75);
    }

    .item-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 10px 0;
        border-bottom: 1px solid #333;
    }

    .item-row:last-child {
        border-bottom: none;
    }

    .item-label {
        font-weight: 700;
        color: #999;
        flex: 1 1 30%;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        user-select: none;
    }

    .item-value {
        flex: 1 1 55%;
        color: #eee;
        font-weight: 600;
        word-break: break-word;
    }

    .check-btn {
        flex: 0 0 auto;
        margin-left: 12px;
        padding: 6px 14px;
        font-size: 14px;
        cursor: pointer;
        border: none;
        border-radius: 8px;
        background-color: #3a86ff;
        color: white;
        box-shadow: 0 4px 10px rgba(58, 134, 255, 0.6);
        transition: background-color 0.35s ease, box-shadow 0.35s ease, transform 0.2s ease;
    }

    .check-btn:hover {
        background-color: #1e5bb8;
        box-shadow: 0 6px 14px rgba(30, 91, 184, 0.8);
        transform: scale(1.05);
    }

    .check-result {
        flex: 0 0 auto;
        margin-left: 14px;
        font-weight: 700;
        user-select: none;
        min-width: 130px;
        text-align: center;
        color: #e0e0e0;
    }

    /* Error messages */
    .error-message {
        background-color: #330000;
        border: 1px solid #aa0000;
        color: #ff6666;
        padding: 14px 18px;
        margin-bottom: 16px;
        border-radius: 12px;
        max-width: 600px;
        font-weight: 700;
        font-family: monospace;
        box-shadow: 0 4px 16px rgba(170, 0, 0, 0.6);
    }

    .warning-message {
        background-color: #443300;
        border: 1px solid #ccaa00;
        color: #ffdd55;
        padding: 14px 18px;
        margin-bottom: 16px;
        border-radius: 12px;
        max-width: 600px;
        font-weight: 700;
        font-family: monospace;
        box-shadow: 0 4px 16px rgba(204, 170, 0, 0.6);
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #222;
        margin: 10% auto;
        padding: 28px 32px;
        border-radius: 14px;
        border: 1px solid #555;
        width: 90%;
        max-width: 500px;
        color: #eee;
        text-align: center;
        position: relative;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.7);
    }

    .modal-header {
        font-size: 22px;
        margin-bottom: 16px;
        font-weight: 800;
        letter-spacing: 0.07em;
    }

    .modal-close {
        position: absolute;
        top: 12px;
        right: 20px;
        font-size: 26px;
        font-weight: 900;
        cursor: pointer;
        color: #bbb;
        user-select: none;
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: #fff;
    }

    #modal-reader {
        margin: 15px auto 20px;
        width: 100%;
        max-width: 500px;
        aspect-ratio: 4 / 2;
        border-radius: 10px;
        border: 1px solid #666;
        background: #111;
        box-shadow: inset 0 0 15px #3a6aff88;
    }

    #modal-result {
        margin-top: 20px;
        font-size: 19px;
        font-weight: 700;
        user-select: none;
    }

    #item-list {
        width: 90%;
        max-width: 600px;
        margin: 0 auto;
        padding-left: 12px;
        box-sizing: border-box;
    }
</style>

<div id="reader"></div><br>
<p style="padding-left: 20px;">読み取った件数: <span id="item-count">0</span> 件</p>
<div id="item-list"></div>


<!-- Modal -->
<div id="checkModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="modalCloseBtn">&times;</span>
        <div class="modal-header">検査する部品: <span id="modal-label"></span></div>
        <div id="modal-reader"></div>
        <div id="modal-result">比較のために部品をスキャンしてください</div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // เก็บรายการรหัสที่ถูกสแกนไว้ เพื่อป้องกันการสแกนซ้ำ
    let scannedItems = new Set();

    // ดึงองค์ประกอบใน DOM ที่ใช้แสดงผล
    const reader = document.getElementById('reader');                // กล่องสแกนหลัก
    const itemCount = document.getElementById('item-count');         // แสดงจำนวนรายการที่สแกน
    const itemList = document.getElementById('item-list');           // รายการข้อมูลที่แสดง

    // Modal สำหรับตรวจสอบไส้
    const modal = document.getElementById('checkModal');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalLabel = document.getElementById('modal-label');       // แสดงประเภทไส้ที่กำลังตรวจสอบ
    const modalResult = document.getElementById('modal-result');     // แสดงผลลัพธ์จากการตรวจสอบ
    const modalReaderId = 'modal-reader';                            // ID ของตัวสแกนใน modal

    // สร้างอินสแตนซ์ของ html5-qrcode สำหรับสแกน
    let html5QrcodeMain = new Html5Qrcode("reader");                 // สแกนเนอร์หลัก
    let html5QrcodeModal = null;                                     // สแกนเนอร์ใน modal

    // ฟังก์ชันสร้างแถวสำหรับการตรวจสอบไส้แต่ละประเภท (MAIN, NT, W, ฯลฯ)
    // เพิ่มพารามิเตอร์ isChecked เช็คสถานะว่าไส้นั้นผ่านการตรวจสอบหรือยัง
    function createCheckRow(label, value, isChecked) {
        if (isChecked) {
            return `
        <div class="item-row" data-label="${label}">
            <div class="item-label">${label}</div>
            <div class="item-value">${value}</div>
            <span class="check-result">✅</span>
        </div>
        `;
        } else {
            return `
        <div class="item-row" data-label="${label}">
            <div class="item-label">${label}</div>
            <div class="item-value">${value}</div>
            <button class="check-btn" data-value="${value}" data-label="${label}">検査</button>
            <span class="check-result"></span>
        </div>
        `;
        }
    }

    // ล้างข้อมูลทั้งหมด
    function clearAll() {
        scannedItems.clear();
        itemList.innerHTML = '';
        itemCount.textContent = '0';
    }

    // สร้างเอฟเฟกต์เมื่อสแกนสำเร็จ (กระพริบ)
    function flashSuccess() {
        reader.classList.add('success-flash');
        setTimeout(() => {
            reader.classList.remove('success-flash');
        }, 500);
    }

    // อัปเดตจำนวนรายการที่สแกนได้
    function updateUI() {
        itemCount.textContent = scannedItems.size;
    }

    // เริ่มต้นกล้องสแกนหลัก
    function startMainScanner() {
        const config = {
            fps: 10,  // ความเร็วในการสแกน
            qrbox: { width: 150, height: 50 },
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };
        html5QrcodeMain.start(
            { facingMode: "environment" }, // กล้องหลัง
            config,
            (decodedText) => {
                flashSuccess();

                // ถ้ายังไม่เคยสแกนมาก่อน
                if (!scannedItems.has(decodedText)) {
                    clearAll(); // ล้างรายการเดิมก่อน
                    checkOrderNoInDB(decodedText); // ตรวจสอบข้อมูลจากเซิร์ฟเวอร์
                }
            },
            (errorMessage) => { /* ไม่ทำอะไรเมื่อสแกนพลาด */ }
        ).catch(err => {
            console.error("Unable to start main scanner.", err);
        });
    }

    // หยุดกล้องสแกนหลัก
    function stopMainScanner() {
        return html5QrcodeMain.stop().then(() => {
            html5QrcodeMain.clear();
        }).catch(() => { });
    }

    // ตรวจสอบเลขออเดอร์กับฐานข้อมูล (ผ่าน API)
    function checkOrderNoInDB(order_no) {
        console.log("กำลังตรวจสอบ: ", order_no);
        fetch('check_order_no.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_no: order_no })
        })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    if (!scannedItems.has(order_no)) {
                        scannedItems.add(order_no);

                        const card = document.createElement('div');
                        card.classList.add('item-card');

                        // สร้างเนื้อหา card แสดงรายละเอียดของออเดอร์ พร้อมแสดงสถานะตรวจสอบไส้ (Y = ตรวจสอบแล้ว)
                        card.innerHTML = `
                        <div class="item-row"><div class="item-label">番号</div><div class="item-value">1</div></div>
                        <div class="item-row"><div class="item-label">顧客</div><div class="item-value">${data.data.customer}</div></div>
                        <div class="item-row"><div class="item-label">注文番号</div><div class="item-value">${data.data.order_no}</div></div>
                        <div class="item-row"><div class="item-label">商品名</div><div class="item-value">${data.data.nameproduct}</div></div>
                        <div class="item-row"><div class="item-label">数量 (QTY)</div><div class="item-value">${data.data.qtymain}</div></div>
                        ${createCheckRow('MAIN', data.data.main, data.data.mainstatus === 'Y')}
                        ${createCheckRow('NT', data.data.nt, data.data.ntstatus === 'Y')}
                        ${createCheckRow('W', data.data.w, data.data.wstatus === 'Y')}
                        ${createCheckRow('SW', data.data.sw, data.data.swstatus === 'Y')}
                        ${createCheckRow('TW', data.data.tw, data.data.twstatus === 'Y')}
                        ${createCheckRow('CS', data.data.cs, data.data.csstatus === 'Y')}
                    `;

                        itemList.appendChild(card);
                        updateUI();
                    }
                } else {
                    // กรณีไม่พบข้อมูล
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('error-message');
                    errorDiv.textContent = `❌ データが見つかりませんでした: ${order_no}`;
                    itemList.appendChild(errorDiv);
                }
            })
            .catch(err => {
                // กรณีเชื่อมต่อเซิร์ฟเวอร์ผิดพลาด
                console.error('Fetch error:', err);
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('warning-message');
                errorDiv.textContent = `⚠️ サーバー接続エラーが発生しました: ${order_no}`;
                itemList.appendChild(errorDiv);
            });
    }

    // เปิด modal เพื่อตรวจสอบไส้
    function openCheckModal(label, expectedValue) {
        modalLabel.textContent = label;
        modalResult.textContent = '比較のために部品をスキャンしてください';
        modalResult.style.color = 'white';

        modal.style.display = 'block';
        stopMainScanner();

        // เตรียม html5QrcodeModal
        if (html5QrcodeModal) {
            try {
                html5QrcodeModal.clear();
            } catch (err) {
                console.warn('⚠️ clear() ล้มเหลวหรือไม่รองรับ:', err);
            }
        } else {
            html5QrcodeModal = new Html5Qrcode(modalReaderId);
        }

        const config = {
            fps: 15,
            qrbox: { width: 350, height: 150 },
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };

        // เริ่มสแกนภายใน modal
        function startModalScan() {
            html5QrcodeModal.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    html5QrcodeModal.stop().then(() => {
                        html5QrcodeModal.clear();
                    }).catch(() => { });

                    // ถ้าค่าที่สแกนตรงกัน
                    if (decodedText === expectedValue) {
                        const updatePayload = {
                            order_no: getCurrentOrderNo(),
                            part_type: label.toLowerCase(),
                            scanned_value: 'Y'
                        };

                        // ส่งข้อมูลเพื่ออัปเดตสถานะ
                        fetch('update_status.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(updatePayload)
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('✅ อัปเดตสถานะสำเร็จ');
                                    // อัปเดต UI เปลี่ยนปุ่มเป็นติ๊กถูก
                                    updateCheckRowStatus(label);
                                } else {
                                    console.warn('❌ อัปเดตไม่สำเร็จ:', data.message || '');
                                }
                            })
                            .catch(err => {
                                console.error('⚠️ เกิดข้อผิดพลาดในการอัปเดต:', err);
                            });

                        modalResult.textContent = '✅ 一致しました！';
                        modalResult.style.color = 'lightgreen';

                        // ปิด modal หลัง 5 วิ แล้วเปิดกล้องหลักใหม่
                        setTimeout(() => {
                            modal.style.display = 'none';
                            startMainScanner();
                        }, 5000);
                    } else {
                        // ถ้าค่าที่สแกนไม่ตรงกัน
                        modalResult.textContent = `❌ 一致しません！ (読み取り値: ${decodedText})`;
                        modalResult.style.color = 'orangered';

                        // สแกนใหม่หลัง 5 วิ
                        setTimeout(() => {
                            modalResult.textContent = '比較のために部品をスキャンしてください';
                            modalResult.style.color = 'white';
                            startModalScan();
                        }, 5000);
                    }
                },
                (error) => {
                    // ไม่ต้องทำอะไรเมื่ออ่านไม่สำเร็จ
                }
            ).catch(err => {
                modalResult.textContent = '⚠️ カメラを起動できませんでした';
                modalResult.style.color = 'orange';
                console.error("ไม่สามารถเริ่มกล้อง modal:", err);
            });
        }

        startModalScan(); // เริ่มการสแกน modal
    }

    // ฟังก์ชันอัปเดต UI เปลี่ยนปุ่ม "検査" เป็นติ๊กถูก ✅ เมื่ออัปเดตสถานะสำเร็จ
    function updateCheckRowStatus(label) {
        const rows = document.querySelectorAll(`.item-row[data-label="${label}"]`);
        rows.forEach(row => {
            const btn = row.querySelector('button.check-btn');
            if (btn) {
                btn.remove(); // ลบปุ่มออก
            }
            const checkResult = row.querySelector('.check-result');
            if (checkResult) {
                checkResult.textContent = '✅';
            }
        });
    }

    // ปิด modal ด้วยปุ่ม ×
    modalCloseBtn.onclick = function () {
        modal.style.display = 'none';
        if (html5QrcodeModal) {
            html5QrcodeModal.stop().then(() => {
                html5QrcodeModal.clear();
            }).catch(() => { });
        }
        startMainScanner();
    }

    // ปิด modal เมื่อคลิกด้านนอก
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            if (html5QrcodeModal) {
                html5QrcodeModal.stop().then(() => {
                    html5QrcodeModal.clear();
                }).catch(() => { });
            }
            startMainScanner();
        }
    }

    // ตรวจจับคลิกปุ่ม "検査" ในไส้แต่ละรายการ
    itemList.addEventListener('click', (e) => {
        if (e.target.classList.contains('check-btn')) {
            const btn = e.target;
            const expectedValue = btn.getAttribute('data-value');
            const label = btn.getAttribute('data-label');
            openCheckModal(label, expectedValue);
        }
    });

    // ดึงหมายเลขออเดอร์ปัจจุบันจาก DOM
    function getCurrentOrderNo() {
        const rows = document.querySelectorAll('.item-card .item-row');
        for (const row of rows) {
            const label = row.querySelector('.item-label');
            if (label && label.innerText.trim() === '注文番号') {
                const value = row.querySelector('.item-value');
                return value ? value.innerText.trim() : '';
            }
        }
        return '';
    }

    // เริ่มการสแกนหลักเมื่อโหลดหน้า
    startMainScanner();


</script>



</body>

</html>