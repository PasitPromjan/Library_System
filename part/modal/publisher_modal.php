<div class="modal fade" id="publisherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border border-danger">
            <div class="modal-header align-items-center bg-gradient-danger text-white">
                <h5 class="modal-title">สำนักพิมพ์</h5>
                <button class="btn btn-light" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="publisherForm">
                    <div class="form-group">
                        <label>ชื่อสำนักงานพิมพ์</label>
                        <input type="text" id="publisherName" class="form-control" placeholder="เพิ่มชื่อสำนักงานพิมพ์">
                    </div>
                    <p class="err-validate text-danger" id="validate-publisherName"></p>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gradient-danger text-white" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                    <span>ปิด</span>
                </button>
                <button id="publisherSubmit" class="btn bg-gradient-secondary text-white">
                    บันทึก
                </button>
            </div>
        </div>
    </div>
</div>
