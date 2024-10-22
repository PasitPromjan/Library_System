<div class="modal fade" id="categoryBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border border-danger">
            <div class="modal-header align-items-center bg-gradient-danger text-white">
                <h5 class="modal-title">เพิ่มหมวดหมู่</h5>
                <button class="btn btn-light" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>หมวดหมู่</label>
                    <input type="text" id="categoryBook" class="form-control" placeholder="ป้อนหมวดหมู่หนังสือ">
                </div>
                <p class="err-validate text-danger" id="validate-categoryBook"></p>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gradient-danger text-white" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                    <span>ปิด</span>
                </button>
                <button id="categorySubmit" class="btn bg-gradient-secondary text-white">
                    บันทึก
                </button>
            </div>
        </div>
    </div>
</div>
